<?php

namespace App\Extensions\Modules;

use Illuminate\Support\ClassLoader;

use Log;
use View;

use App\Game;

class GameModule
{
	protected $game;
	public function __construct($game)
	{
		$this->game = $game;
	}

	public static function CreateInstance($game)
	{
		$class = 'Modules\\Games\\' . $game->GModulePHP;

		if (!class_exists($class, true))
			return new GameModule($game);

		$reflect = new \ReflectionClass($class);
		return $reflect->newInstance($game);
	}

	//////////////////////////////////////////////////

	public function makeView($v, $args)
	{
		if (empty($this->game))
			return View::make($v, $args);

		$str = $this->game->GAbbr.'.'.$v;
		if (View::exists($str))
			return View::make($str, $args);

		return View::make($v, $args);
	}

	//////////////////////////////////////////////////

	public function memberAdded($gameuser)
	{
		return true;
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
