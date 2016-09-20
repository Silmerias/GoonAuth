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

	public function getOrg()
	{
		return $this->org;
	}

	public function getGame()
	{
		return $this->game;
	}

	//////////////////////////////////////////////////

	public function simpleRegistration()
	{
		// If true, and this is the only org attached to a game, integrate
		// the join process.  If we require seperate authentication, then set
		// this to FALSE!  This will automatically approve memberships.
		return true;
	}

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
