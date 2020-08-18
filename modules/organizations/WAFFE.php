<?php

namespace Modules\Organizations;
use App\Extensions\Modules\OrgModule;

use App\GameUser;

class WAFFE extends OrgModule
{
	public function simpleRegistration()
	{
		return false;
	}

	public function memberAdded($gameuser)
	{
		if ($this->game->GAbbr !== 'ee')
			return false;
		return true;
	}

	public function memberRejected($gameuser)
	{
	}

	public function memberKicked($gameuser)
	{
	}
}
