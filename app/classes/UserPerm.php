<?php

class UserPerm
{
	private $perms = array();
	private $empty = array(
		'add' => false,
		'remove' => false,
		'modify' => false,
		'auth' => false,
		'read' => false
		);

	function __construct($user)
	{
		$this->perms['group'] = $this->empty;
		$this->perms['game'] = array();

		$group = DB::table('GroupAdmin')
			->join('Role', 'GroupAdmin.RID', '=', 'Role.RID')
			->where('GroupAdmin.UID', $user->UID)
			->where('GroupAdmin.GRID', $user->UGroup)
			->select('Role.RPermAdd as RPermAdd', 'Role.RPermRemove as RPermRemove', 'Role.RPermModify as RPermModify', 'Role.RPermAuth as RPermAuth', 'Role.RPermRead as RPermRead')
			->get();

		$game = DB::table('GameOrgAdmin')
			->join('GameOrg', 'GameOrgAdmin.GOID', '=', 'GameOrg.GOID')
			->join('Role', 'GameOrgAdmin.RID', '=', 'Role.RID')
			->where('GameOrgAdmin.UID', $user->UID)
			->select('Role.RPermAdd as RPermAdd', 'Role.RPermRemove as RPermRemove', 'Role.RPermModify as RPermModify', 'Role.RPermAuth as RPermAuth', 'Role.RPermRead as RPermRead')
			->addSelect('GameOrg.GOID as GOID')
			->get();

		foreach ($group as $e)
		{
			$a = $this->perms['group'];

			$a['add'] |= $e->RPermAdd;
			$a['remove'] |= $e->RPermRemove;
			$a['modify'] |= $e->RPermModify;
			$a['auth'] |= $e->RPermAuth;
			$a['read'] |= $e->RPermRead;

			$this->perms['group'] = $a;
		}

		foreach ($game as $e)
		{
			$a = $this->empty;
			if (isset($this->perms['game'][$e->GOID]))
				$a = $this->perms['game'][$e->GOID];

			$a['add'] |= $e->RPermAdd;
			$a['remove'] |= $e->RPermRemove;
			$a['modify'] |= $e->RPermModify;
			$a['auth'] |= $e->RPermAuth;
			$a['read'] |= $e->RPermRead;

			$this->perms['game'][$e->GOID] = $a;
		}
	}

	public function gameOrg($goid)
	{
		if (isset($this->perms['game'][$goid]))
			return (object)$this->perms['game'][$goid];
		else return (object)$this->empty;
	}

	public function group()
	{
		return (object)$this->perms['group'];
	}
}
