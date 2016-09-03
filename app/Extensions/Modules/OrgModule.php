<?php

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
		$class = $org->GOModulePHP;

		ClassLoader::load($class);
		if (!class_exists($class, false))
			return new OrgModule($org, $game);

		$reflect = new ReflectionClass($class);
		return $reflect->newInstance($org, $game);
	}

	//////////////////////////////////////////////////

	public function makeView($v, $args)
	{
		$v = str_replace('orgs::', '', $v);

		if (empty($this->game) && empty($this->org))
			return View::make('orgs::'.$v, $args);

		$str = 'orgs::'.$this->game->GAbbr.'.'.$this->org->GOAbbr.'.'.$v;
		if (View::exists($str))
			return View::make($str, $args);

		$str = 'orgs::'.$this->org->GOAbbr.'.'.$v;
		if (View::exists($str))
			return View::make($str, $args);

		return View::make('orgs::'.$v, $args);
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
