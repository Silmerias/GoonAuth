<?php

class GroupController extends BaseController
{
	public function showGroup($grid)
	{
		$auth = Session::get('auth');
		$group = Group::find($grid);
		if (empty($group))
			return Redirect::to('/');

		$include = array('auth' => $auth, 'group' => $group);
		return View::make('group.list', $include);
	}

	public function showAuth($grid)
	{
		$auth = Session::get('auth');
		$group = Group::find($grid);
		if (empty($group))
			return Redirect::to('/');

		$include = array('auth' => $auth, 'group' => $group);
		return View::make('group.auth', $include);
	}

	public function doAuth($grid, $uid)
	{
		$action = Input::get('action');
		$user = User::find($uid);

		if (strcasecmp($action, "approve") == 0)
		{
			$active = UserStatus::where('USCode', 'ACTI')->first();
			$user->USID = $active->USID;
			$user->save();
		}
		else
		{
			$rejected = UserStatus::where('USCode', 'REJE')->first();
			$user->USID = $rejected->USID;
			$user->save();
		}

		return Response::json(array(
			'success' => true
		));
	}
}
