<?php

class GameModule
{
	protected $game;
	public function __construct($game)
	{
		$this->game = $game;
	}

	public static function CreateInstance($game)
	{
		$class = $game->GModulePHP;

		ClassLoader::load($class);
		if (!class_exists($class, false))
			return new GameModule($game);

		$reflect = new ReflectionClass($class);
		return $reflect->newInstance($game);
	}

	//////////////////////////////////////////////////

	public function makeView($v, $args)
	{
		$v = str_replace('games::', '', $v);

		if (empty($this->game))
			return View::make('games::'.$v, $args);

		$str = 'games::'.$this->game->GAbbr.'.'.$v;
		if (View::exists($str))
			return View::make($str, $args);

		return View::make('games::'.$v, $args);
	}

	//////////////////////////////////////////////////

	public function memberAdded($gameuser)
	{
	}

	public function memberKicked($gameuser)
	{
	}

	public function memberVerify($username, $token)
	{
		return true;
	}

	public function memberProfile($gameuser)
	{
		return e($gameuser->GUCachedName);
	}
}
