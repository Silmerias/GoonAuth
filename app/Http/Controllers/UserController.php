<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Auth;
use DB;
use Input;
use Redirect;
use Session;
use View;

use App\User;

class UserController extends Controller
{

	public function showHome()
	{
		return view('user.home');
	}

	public function doLogin()
	{
		$credentials = array(
			'username' => Input::get('goonid'),
			'password' => Input::get('password')
		);

		// Do the authentication!
		if (Auth::attempt($credentials, Input::has('persist') ? true : false))
			return Redirect::intended('/');

		// Uh oh.
		Session::flash('error', 'Invalid username/password.');
		return view('user.login');
	}

	public function showUser($id)
	{
		$user = User::find($id);
		$notes = $this->getNotes($user);

		$include = array('user' => $user, 'notes' => $notes);
		return view('user.stats', $include);
	}

	public function showNotes($id)
	{
		$user = User::find($id);
		$notes = $this->getNotes($user);

		$include = array('notes' => $notes);
		return view('user.notes', $include);
	}

	public function getNotes($user)
	{
		$auth = Auth::user();

		// Get all groups the user has permissions in.
		$groups = DB::table('Group')
			->join('GroupAdmin', 'Group.GRID', '=', 'GroupAdmin.GRID')
			->join('Role', 'GroupAdmin.RID', '=', 'Role.RID')
			->where('GroupAdmin.UID', '=', $auth->UID)
			->where('Role.RPermRead', '=', true)
			->get();

		// Get all the orgs the user has permissions in.
		$orgs = DB::table('GameOrg')
			->join('GameOrgAdmin', 'GameOrg.GOID', '=', 'GameOrgAdmin.GOID')
			->join('Role', 'GameOrgAdmin.RID', '=', 'Role.RID')
			->where('GameOrgAdmin.UID', '=', $auth->UID)
			->where('Role.RPermRead', '=', true)
			->get();

		// Assemble the list of ids.
		$grid = array();
		$goid = array();
		foreach ($groups as $e)
			$grid[] = $e->GRID;
		foreach ($orgs as $e)
			$goid[] = $e->GOID;

		$notes = null;
		if (!empty($grid) || !empty($goid))
		{
			// Find all valid notes.
			$notes = DB::table('Note')
				->join('NoteType', 'Note.NTID', '=', 'NoteType.NTID')
				->leftJoin('User', 'Note.UID', '=', 'User.UID')
				->leftJoin('User AS Created', 'Note.NCreatedByUID', '=', 'Created.UID')
				->select('Note.NID as NID', 'Note.NSubject as NSubject', 'Note.NMessage as NMessage', 'Note.NTimestamp as NTimestamp')
				->addSelect('NoteType.NTColor as NTColor', 'NoteType.NTName as NTName')
				->addSelect('User.UGoonID as UGoonID', 'Created.UGoonID as CreatedGoonID', 'Created.UID as CreatedUID')
				->addSelect('Note.NGlobal as NGlobal')
				->orderBy('Note.NTimestamp', 'ASC');

			if (!empty($grid))
				$notes->leftJoin('GroupHasNote', 'Note.NID', '=', 'GroupHasNote.NID');
			if (!empty($goid))
				$notes->leftJoin('GameOrgHasNote', 'Note.NID', '=', 'GameOrgHasNote.NID');

			$notes->where('User.UID', $user->UID);
			$notes->where(function($q) use($grid, $goid) {
				if (!empty($grid))
					$q->orWhereIn('GroupHasNote.GRID', $grid);
				if (!empty($goid))
					$q->orWhereIn('GameOrgHasNote.GOID', $goid);

				// Check for global notes.
				$q->orWhere('Note.NGlobal', true);
			});

			$notes = $notes->get();
		}

		return $notes;
	}
}
