<?php namespace LemonTree;

use Illuminate\Routing\UrlGenerator;

class CustomUrlGenerator extends UrlGenerator {

	/**
	 * Format the given URL segments into a single URL.
	 *
	 * @param  string  $root
	 * @param  string  $path
	 * @param  string  $tail
	 * @return string
	 */
	protected function trimUrl($root, $path, $tail = '')
	{
		return trim($root.'/'.trim($path.'/'.$tail, '/'), '/').'/';
	}

}

?>
