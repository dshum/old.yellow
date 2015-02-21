<?php namespace LemonTree;

class LoggedUser {

	private static $user = null;

	public static function create()
	{
		return new self();
	}

	public static function setUser($user)
	{
		if ($user) {
			static::$user = $user;
		}
	}

	public static function getUser()
	{
		return static::$user;
	}

	public static function isLogged()
	{
		return static::$user ? true : false;
	}

}
