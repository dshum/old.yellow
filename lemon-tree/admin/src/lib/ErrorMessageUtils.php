<?php namespace LemonTree;

use Carbon\Carbon;

class ErrorMessageUtils {

	const TIME_DELAY = 60;

	public static function sendMessage(\Exception $e)
	{
		if (
			! \Config::get('mail.from.address')
			|| ! \Config::get('mail.buglover.address')
		) {
			return false;
		}

		$server =
			isset($_SERVER['HTTP_HOST'])
			? $_SERVER['HTTP_HOST']
			: (defined('HTTP_HOST') ? HTTP_HOST : '');

		$uri =
			isset($_SERVER['REQUEST_URI'])
			? $server.$_SERVER['REQUEST_URI']
			: $_SERVER['PHP_SELF'];

		$ip =
			isset($_SERVER['HTTP_X_REAL_IP'])
			? $_SERVER['HTTP_X_REAL_IP']
			: isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

		$ip2 =
			isset($_SERVER['HTTP_X_FORWARDED_FOR'])
			? $_SERVER['HTTP_X_FORWARDED_FOR']
			: isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

		$useragent =
			isset($_SERVER['HTTP_USER_AGENT'])
			? $_SERVER['HTTP_USER_AGENT']
			: '';

		$referer =
			isset($_SERVER['HTTP_REFERER'])
			? $_SERVER['HTTP_REFERER']
			: '';

		$method =
			isset($_SERVER['REQUEST_METHOD'])
			? $_SERVER['REQUEST_METHOD']
			: '';

		$exception = get_class($e);

		$get = var_export($_GET, true);
		$post = var_export($_POST, true);
		$cookie = var_export($_COOKIE, true);

		$filename = md5(
			$exception.' - '.$e->getMessage().' - '.$e->getTraceAsString()
		);

		$send = false;
		$count = 0;
		$diff = 0;

		$filepath = storage_path().'/logs/'.$filename;

		if (file_exists($filepath)) {
			$time = filemtime($filepath);
			if (time() - $time > static::TIME_DELAY) {
				$count = static::reset($filepath);
				$diff = time() - $time;
				$send = true;
			} else {
				static::increment($filepath);
			}
		} else {
			static::reset($filepath);
			$send = true;
		}

		$date = Carbon::now();

		$subject = $uri.' - '.$exception.' - '.$e->getMessage();

		$data = array(
			'e' => $e,
			'server' => $server,
			'uri' => $uri,
			'ip' => $ip,
			'ip2' => $ip2,
			'useragent' => $useragent,
			'referer' => $referer,
			'method' => $method,
			'exception' => $exception,
			'get' => $get,
			'post' => $post,
			'cookie' => $cookie,
			'count' => $count,
			'diff' => $diff,
			'date' => $date,
		);

		\Mail::send('admin::mail.error', $data, function($message) use ($subject) {
			$message->
				from(\Config::get('mail.from.address'), \Config::get('mail.from.name'))->
				to(\Config::get('mail.buglover.address'), \Config::get('mail.buglover.name'))->
				subject($subject);
		});
	}

	public static function printMessage(\Exception $e)
	{
		$server =
			isset($_SERVER['HTTP_HOST'])
			? $_SERVER['HTTP_HOST']
			: (defined('HTTP_HOST') ? HTTP_HOST : '');

		$uri =
			isset($_SERVER['REQUEST_URI'])
			? $server.$_SERVER['REQUEST_URI']
			: '';

		$ip =
			isset($_SERVER['HTTP_X_REAL_IP'])
			? $_SERVER['HTTP_X_REAL_IP']
			: isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

		$useragent =
			isset($_SERVER['HTTP_USER_AGENT'])
			? $_SERVER['HTTP_USER_AGENT']
			: '';

		$referer =
			isset($_SERVER['HTTP_REFERER'])
			? $_SERVER['HTTP_REFERER']
			: '';

		$method =
			isset($_SERVER['REQUEST_METHOD'])
			? $_SERVER['REQUEST_METHOD']
			: '';

		$exception = get_class($e);

		$trace =
			strpos($e->getMessage(), 'mysql_connect') === false
			? nl2br($e->getTraceAsString())
			: null;

		$get = var_export($_GET, true);
		$post = var_export($_POST, true);
		$cookie = var_export($_COOKIE, true);

		$str = <<<HTML
Class: $exception<br />
Message: {$e->getMessage()}<br />
File: {$e->getFile()}<br />
Line: {$e->getLine()}<br />
Code: {$e->getCode()}<br />
Trace: {$trace}<br /><br />
Server: $server<br />
URI: $uri<br />
IP: $ip<br />
UserAgent: $useragent<br />
Referer: $referer<br />
Request method: $method<br />
GET: <pre>$get</pre><br />
POST: <pre>$post</pre><br />
COOKIE: <pre>$cookie</pre><br />
HTML;

		return $str;
	}

	protected static function reset($filepath)
	{
		$count = 0;

		if (is_readable($filepath)) {
			$f = fopen($filepath, 'r');
			$count = floor(fread($f, 4096));
			fclose($f);
		}

		$f = fopen($filepath, 'w');
		fwrite($f, 1);
		fclose($f);

		return $count;
	}

	protected static function increment($filepath)
	{
		$count = 0;

		if (is_readable($filepath)) {
			$f = fopen($filepath, 'r');
			$count = floor(fread($f, 4096));
			fclose($f);
		}

		$f = fopen($filepath, 'w');
		fwrite($f, ++$count);
		fclose($f);

		return $count;
	}

}
