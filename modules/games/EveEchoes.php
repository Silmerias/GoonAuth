<?php

namespace Modules\Games;
use App\Extensions\Modules\GameModule;

use Carbon\Carbon;

use App\GameUser;

class EveEchoes extends GameModule
{
	public function memberAdded($gameuser)
	{
		return true;
	}

	public function memberRejected($gameuser)
	{
	}

	public function memberKicked($gameuser)
	{
	}

	public function memberVerify($username, $token)
	{
		// No API / Website for member validation yet exist in Eve Echoes
		return true;
	}
}
