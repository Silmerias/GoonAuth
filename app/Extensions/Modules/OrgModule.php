<?php

namespace App\Extensions\Modules;

use Illuminate\Support\ClassLoader;

use View;

use App\Game;
use App\GameOrg;

class OrgModule
{
	protected $org;
	protected $game;
	public function __construct($org, $game)
	{
		$this->org = $org;
		$this->game = $game;
	}

	public static function CreateInstance($org, $game)
	{
		$class = 'Modules\\Organizations\\' . $org->GOModulePHP;

		if (!class_exists($class, true))
			return new OrgModule($org, $game);

		$reflect = new \ReflectionClass($class);
		return $reflect->newInstance($org, $game);
	}

	//////////////////////////////////////////////////

	public function makeView($v, $args)
	{
		if (empty($this->game) && empty($this->org))
			return View::make($v, $args);

		$str = $this->game->GAbbr.'.'.$this->org->GOAbbr.'.'.$v;
		if (View::exists($str))
			return View::make($str, $args);

		$str = $this->org->GOAbbr.'.'.$v;
		if (View::exists($str))
			return View::make($str, $args);

		return View::make($v, $args);
	}

	//////////////////////////////////////////////////

	public function memberAdded($gameuser)
	{
	}

	public function memberRejected($gameuser)
	{
	}

	public function memberKicked($gameuser)
	{
	}
}
