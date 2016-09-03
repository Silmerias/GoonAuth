<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Carbon\Carbon;

use Auth;
use Input;
use Log;
use Mail;
use Redirect;
use Response;
use Session;
use View;

use App\Game;
use App\GameOrg;
use App\GameUser;
use App\NoteType;
use App\User;
use App\UserStatus;

use App\Extensions\LDAP\LDAP;
use App\Extensions\Modules\GameModule;
use App\Extensions\Modules\OrgModule;
use App\Extensions\Notes\NoteHelper;
use App\Extensions\Permissions\UserPerm;

class GameController extends Controller
{
	public function getRoot()
	{
		$include = array('auth' => Auth::user());
		return View::make('games.list', $include);
	}

	public function getGames($abbr)
	{
		$game = Game::where('GAbbr', $abbr)->first();
		if (empty($game))
			return Redirect::to('games');

		$MOD = GameModule::CreateInstance($game);

		$include = array('game' => $game);
		return $MOD->makeView('games.details', $include);
	}

	public function getGamesLink($abbr)
	{
		$game = Game::where('GAbbr', $abbr)->first();
		if (empty($game))
			return Redirect::to('games');

		$token = uniqid('FART');
		Session::put('token', $token);

		$MOD = GameModule::CreateInstance($game);

		$include = array('game' => $game, 'token' => $token);
		return $MOD->makeView('games.link', $include);
	}

	public function postGamesLink($abbr)
	{
		$auth = Auth::user();
		$game = Game::where('GAbbr', $abbr)->first();
		if (empty($game))
			return Redirect::to('games');

		$username = Input::get('username');
		$token = Session::get('token');

		// Check if the user exists.
		$count = GameUser::where('UID', $auth->UID)->where('GID', $game->GID)->where('GUCachedName', $username)->count();
		if ($count !== 0)
			return Redirect::back()->with('error', 'This game user is already linked to your account.');

		if (!isset($username))
			return Redirect::back()->with('error', 'You must enter your Game Username.');

		$MOD = GameModule::CreateInstance($game);

		// Attempt a verification via the GameModule.
		$ret = $MOD->memberVerify($username, $token);
		if (!isset($ret) || $ret === false || $ret['ret'] === false)
			return Redirect::back()->with('error', 'Verification failed.');

		// Run the GameModule.
		$success = $MOD->memberAdded($user);
		if (isset($success) && $success === false)
			return Redirect::back()->with('error', 'Linking error.  Contact Adeptus for assistance.');

		// Success!  Let's create our user now.
		$user = new GameUser;
		$user->GID = $game->GID;
		$user->UID = $auth->UID;
		$user->GUCacheDate = Carbon::now();
		$user->GUCachedName = $username;
		if (isset($ret) && is_array($ret))
		{
			$user->GUUserID = $ret['userid'];
			$user->GURegDate = $ret['regdate'];
			$user->GUCachedPostCount = $ret['postcount'];
		}
		$user->save();

		$include = array('game' => $game);
		return $MOD->makeView('games.complete', $include);
	}

	public function postGamesLinkCheckUser($abbr)
	{
		$auth = Auth::user();
		$game = Game::where('GAbbr', $abbr)->first();
		if (empty($game))
			return array('valid' => 'false', 'message' => 'Invalid game.');

		$username = Input::get('username');

		// Make sure we got a username.
		if (!isset($username))
			return array('valid' => 'false', 'message' => 'Invalid username.');

		// Check if the user exists.
		$count = GameUser::where('UID', $auth->UID)->where('GID', $game->GID)->where('GUCachedName', $username)->count();
		if ($count !== 0)
			return array('valid' => 'false', 'message' => 'This game user is already linked to your account.');

		return array('valid' => 'true');
	}

	public function getGamesOrg($abbr, $org)
	{
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		if (empty($game) || empty($org))
			return Redirect::to('games');

		$MOD = OrgModule::CreateInstance($org, $game);

		$include = array('game' => $game, 'org' => $org);
		return $MOD->makeView('org.details', $include);
	}

	public function getGamesOrgJoin($abbr, $org)
	{
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		if (empty($game) || empty($org))
			return Redirect::to('games');

		$MOD = OrgModule::CreateInstance($org, $game);

		$include = array('game' => $game, 'org' => $org);
		return $MOD->makeView('org.join', $include);
	}

	public function postGamesOrgJoin($abbr, $org)
	{
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		$gameuser = GameUser::find(Input::get('character'));
		if (empty($game) || empty($org) || empty($gameuser))
			return Redirect::to('games/'.$game->GAbbr.'/'.$org->GOAbbr);

		$pending = UserStatus::where('USCode', 'PEND')->first();
		$gameuser->gameorgs()->attach($org, array('USID' => $pending->USID));

		// Create a note.
		$reg = NoteType::where('NTCode', 'SYS')->first();
		if (!empty($reg))
		{
			$comment = Input::get('comment');
			if (!isset($comment) || empty($comment) || strlen($comment) == 0)
				$comment = '';

			NoteHelper::Add(array(
				'user' => $gameuser->user,
				'createdby' => null,
				'org' => $org,
				'type' => $reg,
				'subject' => $org->GOName.' registration',
				'message' => $comment,
			));
		}

		return Redirect::to('games/'.$game->GAbbr.'/'.$org->GOAbbr);
	}


	public function getGamesOrgView($abbr, $org)
	{
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		if (empty($game) || empty($org))
			return Redirect::to('games');

		$rejected = UserStatus::where('USCode', 'REJE')->first();
		$members = $org->gameusers()
			->join('User', 'GameUser.UID', '=', 'User.UID')
			->orderBy('UGoonID');

		$get = array();
		if (Input::has('goonid'))
		{
			$get['goonid'] = Input::get('goonid');
			$get['goonid-by'] = Input::get('goonid-by');

			$goonid = Input::get('goonid');
			$by = Input::get('goonid-by', 'contains');
			if (strcmp($by, 'starts') === 0) $by = "$goonid%";
			else $by = "%$goonid%";

			$members = $members->where('User.UGoonID', 'LIKE', $by);
			Log::error($by);
		}

		if (Input::has('character'))
		{
			$get['character'] = Input::get('character');
			$get['character-by'] = Input::get('character-by');

			$character = Input::get('character');
			$by = Input::get('character-by', 'contains');
			if (strcmp($by, 'starts') === 0) $by = "$character%";
			else $by = "%$character%";

			$members = $members->where('GameUser.GUCachedName', 'LIKE', $by);
		}

		if (Input::has('status'))
		{
			$get['status'] = Input::get('status');

			$statuses = explode(',', Input::get('status'));
			$members = $members->whereIn('GameOrgHasGameUser.USID', $statuses);
		}
		else $members = $members->where('GameOrgHasGameUser.USID', '<>', $rejected->USID);

		// Paginate!
		$members = $members->paginate(15);

		// Make sure we include our GET parameters.
		if (count($get) !== 0)
			$members = $members->appends($get);

		$MOD = OrgModule::CreateInstance($org, $game);

		$include = array('game' => $game, 'org' => $org, 'members' => $members);
		return $MOD->makeView('org.viewmembers', $include);
	}

	public function postGamesOrgView($abbr, $org)
	{
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		if (empty($game) || empty($org))
			return Response::json(array('success' => false, 'message' => 'Invalid request.  Try logging off and back on.'));

		$user = User::find(intval(Input::get('id')));
		$action = Input::get('action');

		switch ($action)
		{
			case 'kick':
				return Response::json(array('success' => false, 'message' => 'Not yet implemented.'));

			case 'addnote':
				$type = NoteType::find(Input::get('type'));
				$subject = Input::get('subject');
				$message = Input::get('message');
				$global = Input::get('global');

				if (strlen($message) != 0)
				{
					NoteHelper::Add(array(
						'user' => $user,
						'createdby' => Auth::user(),
						'org' => ($global == true ? null : $org),
						'type' => $type,
						'subject' => $subject,
						'message' => $message,
					));
				}

				return Response::json(array('success' => true));
		}

		return Response::json(array('success' => false));
	}

	public function getAuthGamesOrg($abbr, $org)
	{
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		if (empty($game) || empty($org))
			return Redirect::to('games');

		$MOD = OrgModule::CreateInstance($org, $game);

		$include = array('game' => $game, 'org' => $org);
		return $MOD->makeView('org.auth', $include);
	}

	public function postAuthGamesOrg($abbr, $org)
	{
		$action = Input::get('action');
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		$gameuser = GameUser::find(Input::get('id'));
		$user = User::find($gameuser->UID);

		$auth = Auth::user();
		$ntstatus = NoteType::where('NTCode', 'STAT')->first();

		$MOD = OrgModule::CreateInstance($org, $game);

		// Check for auth permission.
		$perms = new UserPerm($auth);
		if ($perms->gameOrg($org->GOID)->auth == false)
		{
			return Response::json(array(
				'success' => false,
				'message' => 'You do not have permission to authorize members.'
			));
		}

		if (strcasecmp($action, "approve") == 0)
		{
			// Run the OrgModule now.
			$success = $MOD->memberAdded($gameuser);
			if (isset($success) && $success === false)
			{
				return Response::json(array(
					'success' => false,
					'message' => 'Could not finish authorization.  Contact Adeptus for assistance.'
				));
			}

			LDAP::Execute(function($ldap) use($user, $org) {
				// Add the user to the organizations's LDAP group.
				$userdn = "cn=" . $user->UGoonID . "," . config('ldap.dn.users');
				$dn = "cn=" . $org->GOLDAPGroup . "," . config('ldap.dn.groups');

				// Delete first.
				@ldap_mod_del($ldap, $dn, array('member' => $userdn));

				// Add to the LDAP group.
				if (@ldap_mod_add($ldap, $dn, array('member' => $userdn)) == false)
				{
					error_log("[ldap] Failed to add user to organization group.");
					error_log("[ldap] Failed to add user to org group: {$org->GOLDAPGroup}.");
					return Response::json(array(
						'success' => false,
						'message' => 'Unable to add user to organization group.  Contact Adeptus for assistance.'
					));
				}
			});

			// Send out the e-mail.
			try
			{
				// Assemble the data required for the e-mail.
				$maildata = [];
				$maildata = array_add($maildata, 'goonid', $user->UGoonID);
				$maildata = array_add($maildata, 'org', $org->GOName);

				$body = $MOD->makeView('emails.game-register-approve', $maildata)->render();

				// Send out the onboarding e-mail.
				Mail::send('emails.echo', ['content' => $body], function($msg) use($org, $user) {
					$msg->subject('Your ' . $org->GOName . ' organization membership request was approved!');
					$msg->to($user->UEmail);
				});
			}
			catch (Exception $e)
			{
				error_log('E-mail error: '.var_dump($e));
				return Response::json(array(
					'success' => false,
					'message' => 'Unable to send e-mail.  Contact Adeptus for assistance.'
				));
			}

			// Add the user.
			$active = UserStatus::where('USCode', 'ACTI')->first();
			$gameuser->gameorgs()->updateExistingPivot($org->GOID,
				array('USID' => $active->USID), false);

			// Create note about the authorization.
			if (!empty($ntstatus))
			{
				NoteHelper::Add(array(
					'user' => $gameuser->user,
					'createdby' => $auth,
					'org' => $org,
					'type' => $ntstatus,
					'subject' => 'Authorization',
					'message' => 'User accepted into organization '.$org->GOName.'.',
				));
			}
		}
		else
		{
			$reason = Input::get('text');

			// Try sending the e-mail out first.
			// If this fails, we want to stop the process until it can be fixed.
			try
			{
				$maildata = [];
				$maildata = array_add($maildata, 'org', $org->GOName);
				$maildata = array_add($maildata, 'reason', $reason);

				$body = $MOD->makeView('emails.game-register-deny', $maildata)->render();

				// Send out the rejection e-mail.
				Mail::send('emails.echo', ['content' => $body], function($msg) use($org, $user) {
					$msg->subject('Your ' . $org->GOName . ' organization membership request was DENIED!');
					$msg->to($user->UEmail);
				});
			}
			catch (Exception $e)
			{
				error_log('E-mail error: '.var_dump($e));
				return Response::json(array(
					'success' => false,
					'message' => 'Unable to send e-mail.  Contact Adeptus for assistance.'
				));
			}

			// Reject the user.
			$rejected = UserStatus::where('USCode', 'REJE')->first();
			$gameuser->gameorgs()->updateExistingPivot($org->GOID,
				array('USID' => $rejected->USID), false);

			// Create note about the rejection.
			if (!empty($ntstatus))
			{
				NoteHelper::Add(array(
					'user' => $gameuser->user,
					'createdby' => $auth,
					'org' => $org,
					'type' => $ntstatus,
					'subject' => 'Authorization',
					'message' => 'User rejected from joining organization '.$org->GOName.".\nReason: ".$reason,
				));
			}

			// Inform the OrgModule that the user was rejected.
			$MOD->memberRejected($gameuser);
		}

		return Response::json(array(
			'success' => true
		));
	}

	public static function buildGameProfile($game, $gameuser)
	{
		$MOD = GameModule::CreateInstance($game);
		return $MOD->memberProfile($gameuser);
	}
}
