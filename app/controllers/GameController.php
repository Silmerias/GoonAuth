<?php

class GameController extends BaseController
{
	protected function verify_sc($username, $token)
	{
		// RSI splits important info into two separate profiles.  WTF guys.
		// We first get the site profile which contains our user id and regdate.
		// We then get the forum profile which contains the postcount, separated into posts and threads.  Because why.

		$ret = array(
			'ret' => false,
			'userid' => 0,
			'regdate' => Carbon::now(),
			'postcount' => 0,
		);

		// Grab our site profile.
		$page = file_get_contents('https://robertsspaceindustries.com/citizens/' . urlencode($username));
		if ($page === FALSE)
			return $ret;

		$pattern404 = "/<title>.*404.*<\/title>/is";
		$patternUserID = "/UEE Citizen Record.*?<strong class=\"value\">#([\w\s,]+)<\/strong>/is";
		$patternEnlisted = "/Enlisted.*?<strong class=\"value\">([\w\s,]+)<\/strong>/is";

		// Test for a 404.  A 404 means that they are not an RSI user.
		// The 404 should have occurred with file_get_contents, so this is just a sanity check.
		if (preg_match($pattern404, $page) === 1)
			return $ret;

		// See if the token was found.
		if (stristr($page, $token) === false)
			return $ret;

		// We are good at this point.
		$ret['ret'] = true;

		// Grab our user ID.
		if (preg_match($patternUserID, $page, $matches) === 1)
			$ret['userid'] = intval($matches[1]);

		// Grab regdate.
		if (preg_match($patternEnlisted, $page, $matches) === 1)
			$ret['regdate'] = Carbon::createFromFormat('M j, Y', $matches[1]);

		// Grab the forum profile now so we can get our post count.
		$page = file_get_contents('https://forums.robertsspaceindustries.com/profile/' . urlencode($username));
		if ($page === FALSE)
			return $ret;

		$patternComments = "/Comments.*?<span class=\"Count\">([\w\s,]+)<\/span>/is";
		$patternDiscussions = "/Discussions.*?<span class=\"Count\">([\w\s,]+)<\/span>.*?<span class=\"Count\">([\w\s,]+)<\/span>/is";

		// Grab our comment count.
		if (preg_match($patternComments, $page, $matches) === 1)
			$ret['postcount'] += intval($matches[1]);

		// Grab our discussion count.
		if (preg_match($patternDiscussions, $page, $matches) === 1)
			$ret['postcount'] += intval($matches[1]);

		return $ret;
	}

	protected function verify_mwo($username, $token)
	{
		// We have to grab two pages to get to the user profile.
		// The first page is a member search so we can get the profile link.

		$ret = array(
			'ret' => false,
			'userid' => 0,
			'regdate' => Carbon::now(),
			'postcount' => 0,
		);

		// Search for our member.
		$page = file_get_contents('http://mwomercs.com/forums/index.php?app=members&module=list&name_box=begins&name=' . urlencode($username));
		if ($page === FALSE)
			return $ret;

		$pattern404 = "/<title>.*404.*<\/title>/is";
		$patternProfile = "/<strong><a href='(.+?)' title='View Profile'>(.+?)<\/a><\/strong>/is";

		// Test for a 404.  A 404 means that they are not an RSI user.
		// The 404 should have occurred with file_get_contents, so this is just a sanity check.
		if (preg_match($pattern404, $page) === 1)
			return $ret;

		// See if the profile was found.
		if (preg_match($patternProfile, $page, $matches) !== 1)
			return $ret;

		// Compare names from the profile.
		if (strcasecmp($matches[2], $username) !== 0)
			return $ret;

		// Grab the profile page.
		$profileURL = $matches[1];
		$page = file_get_contents($profileURL);
		if ($page === FALSE)
			return $ret;

		// See if the token was found.
		if (stristr($page, $token) === false)
			return $ret;

		// We are good at this point.
		$ret['ret'] = true;

		$patternUserID = "/mwomercs\.com\/forums\/user\/(\d+)-.+\//is";
		$patternRegDate = "/Member Since (\d+ \w+ \d+)<br \/>/is";
		$patternPostCount = "/Active Posts.*?<span class='row_data'>([\d,]+).*?<\/span>/is";

		// Grab our user id.
		if (preg_match($patternUserID, $page, $matches) === 1)
			$ret['userid'] = intval($matches[1]);

		// Grab our reg date.
		if (preg_match($patternRegDate, $page, $matches) === 1)
			$ret['regdate'] = Carbon::createFromFormat('j M Y', $matches[1]);

		// Grab our post count.
		if (preg_match($patternPostCount, $page, $matches) === 1)
		{
			$s = str_replace(",", "", $matches[1]);
			$ret['postcount'] = intval($s);
		}

		return $ret;
	}

	public static function buildGameProfile($game, $gameuser)
	{
		if (strcasecmp($game->GAbbr, "mwo") == 0)
			return e($gameuser->GUCachedName);
			//return sprintf($game->GProfileURL, $gameuser->GUUserID, strtolower(str_replace(' ', '-', $gameuser->GUCachedName)));

		if (strcasecmp($game->GAbbr, "sc") == 0)
		{
			$profile = sprintf($game->GProfileURL, $gameuser->GUCachedName);
			$profile = '<a href="'.$profile.'">'.e($gameuser->GUCachedName).'</a>';
			return $profile;
		}
	}

	public function showGames()
	{
		$auth = Session::get('auth');

		$include = array('auth' => $auth);
		return View::make('games.list', $include);
	}

	public function showGame($abbr)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		if (empty($game))
			return Redirect::to('games');

		$include = array('auth' => $auth, 'game' => $game);
		return View::make('games.details', $include);
	}

	public function showGameLink($abbr)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		if (empty($game))
			return Redirect::to('games');

		$token = uniqid('FART');
		Session::put('token', $token);

		$include = array('auth' => $auth, 'game' => $game, 'token' => $token);
		return View::make('games.link', $include);
	}

	public function doGameLink($abbr)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		if (empty($game))
			return Redirect::to('games');

		$username = Input::get('username');
		$token = Session::get('token');

		$requirevalidation = $game->GRequireValidation;

		if (!isset($token) && $requirevalidation)
			return Redirect::back()->with('error', 'An unknown error has occured.');

		if (!isset($username))
			return Redirect::back()->with('error', 'You must enter your Game Username.');

		// Attempt a verification.
		$ret = null;
		if ($requirevalidation)
		{
			$func = 'verify_'.$abbr;
			$ret = $this->$func($username, $token);
			if (!isset($ret) || $ret['ret'] === false)
				return Redirect::back()->with('error', 'Verification failed.');
		}

		// Success!  Let's create our user now.
		$user = new GameUser;
		$user->GID = $game->GID;
		$user->UID = $auth->UID;
		$user->GUCacheDate = Carbon::now();
		$user->GUCachedName = $username;
		if (isset($ret))
		{
			$user->GUUserID = $ret['userid'];
			$user->GURegDate = $ret['regdate'];
			$user->GUCachedPostCount = $ret['postcount'];
		}
		$user->save();

		$include = array('auth' => $auth, 'game' => $game);
		return View::make('games.complete', $include);
	}

	public function showGameOrg($abbr, $org)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		if (empty($game) || empty($org))
			return Redirect::to('games');

		$include = array('auth' => $auth, 'game' => $game, 'org' => $org);
		return View::make('org.details', $include);
	}

	public function showGameOrgMembers($abbr, $org)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		if (empty($game) || empty($org))
			return Redirect::to('games');

		$rejected = UserStatus::where('USCode', 'REJE')->first();
		$members = $org->gameusers()
			->join('User', 'GameUser.UID', '=', 'User.UID')
			->where('GameOrgHasGameUser.USID', '<>', $rejected->USID)
			->orderBy('UGoonID')
			->paginate(15);

		$include = array('auth' => $auth, 'game' => $game, 'org' => $org, 'members' => $members);
		return View::make('org.viewmembers', $include);
	}

	public function doGameOrgMembers($abbr, $org)
	{
		$auth = Session::get('auth');
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
						'createdby' => $auth,
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

	public function showGameOrgJoin($abbr, $org)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		if (empty($game) || empty($org))
			return Redirect::to('games');

		$include = array('auth' => $auth, 'game' => $game, 'org' => $org);
		return View::make('org.join', $include);
	}

	public function doGameOrgJoin($abbr, $org)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		$gameuser = GameUser::find(Input::get('character'));
		if (empty($game) || empty($org) || empty($gameuser))
			return Redirect::to('games/'.$game->GAbbr.'/'.$org->GOAbbr);

		$pending = UserStatus::where('USCode', 'PEND')->first();
		$gameuser->gameorgs()->attach($org, array('USID' => $pending->USID));

		// Create a note if a registration comment was specified.
		$comment = Input::get('comment');
		if (isset($comment) && !empty($comment) && strlen($comment) != 0)
		{
			$reg = NoteType::where('NTCode', 'SYS')->first();
			if (!empty($reg))
			{
				NoteHelper::Add(array(
					'user' => $gameuser->user,
					'createdby' => null,
					'org' => $org,
					'type' => $reg,
					'subject' => $org->GOName." registration comment",
					'message' => $comment,
				));
			}
		}

		return Redirect::to('games/'.$game->GAbbr.'/'.$org->GOAbbr);
	}

	public function showAuth($abbr, $org)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		if (empty($game) || empty($org))
			return Redirect::to('games');

		$include = array('auth' => $auth, 'game' => $game, 'org' => $org);
		return View::make('org.auth', $include);
	}

	public function doAuth($abbr, $org)
	{
		$action = Input::get('action');
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		$gameuser = GameUser::find(Input::get('id'));
		$user = User::find($gameuser->UID);

		$auth = Session::get('auth');
		$ntstatus = NoteType::where('NTCode', 'STAT')->first();

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
			LDAP::Execute(function($ldap) use($user, $org) {
				// Add the user to the organizations's LDAP group.
				$userdn = "cn=" . $user->UGoonID . "," . Config::get('goonauth.ldapDN');
				$dn = "cn=" . $org->GOLDAPGroup . "," . Config::get('goonauth.ldapGroupDN');

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

				// Send out the onboarding e-mail.
				Mail::send('emails.game-register-approve', $maildata, function($msg) use($org, $user) {
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

				// Send out the rejection e-mail.
				Mail::send('emails.game-register-deny', $maildata, function($msg) use($org, $user) {
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

			// Create note about the authorization.
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
		}

		return Response::json(array(
			'success' => true
		));
	}
}
