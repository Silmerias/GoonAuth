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
			return sprintf($game->GProfileURL, $gameuser->GUUserID, strtolower(str_replace(' ', '-', $gameuser->GUCachedName)));

		if (strcasecmp($game->GAbbr, "sc") == 0)
			return sprintf($game->GProfileURL, $gameuser->GUCachedName);
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

		$token = uniqid('FART-');
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

		if (!isset($token))
			return Redirect::back()->with('error', 'An unknown error has occured.');

		if (!isset($username))
			return Redirect::back()->with('error', 'You must enter your Game Username.');

		// Attempt a verification.
		$func = 'verify_'.$abbr;
		$ret = $this->$func($username, $token);
		if (!isset($ret) || $ret['ret'] === false)
			return Redirect::back()->with('error', 'Verification failed.');

		// Success!  Let's create our user now.
		$user = new GameUser;
		$user->GID = $game->GID;
		$user->UID = $auth->UID;
		$user->GUUserID = $ret['userid'];
		$user->GURegDate = $ret['regdate'];
		$user->GUCacheDate = Carbon::now();
		$user->GUCachedName = $username;
		$user->GUCachedPostCount = $ret['postcount'];
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

		$include = array('auth' => $auth, 'game' => $game, 'org' => $org);
		return View::make('org.viewmembers', $include);
	}

	public function doGameOrgMembers($abbr, $org)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		$org = GameOrg::where('GOAbbr', $org)->first();
		if (empty($game) || empty($org))
			return Response::json(array('success' => false));

		$user = User::find(intval(Input::get('id')));
		$action = Input::get('action');

		switch ($action)
		{
			case 'kick':
				return Response::json(array('success' => false));

			case 'addnote':
				$type = NoteType::find(Input::get('type'));
				$text = trim(Input::get('text'));

				if (strlen($text) != 0)
				{
					NoteHelper::Add(array(
						'user' => $user,
						'createdby' => $auth,
						'obj' => $org,
						'type' => $type,
						'text' => $text,
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
					'obj' => $org,
					'type' => $reg,
					'text' => $org->GOName.' registration comment: '.$comment,
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
		$ntauth = NoteType::where('NTCode', 'STAT')->first();

		if (strcasecmp($action, "approve") == 0)
		{
			$active = UserStatus::where('USCode', 'ACTI')->first();
			$gameuser->gameorgs()->updateExistingPivot($org->GOID,
				array('USID' => $active->USID), false);

			LDAP::Execute(function($ldap) {
				// Add the user to the organizations's LDAP group.
				$userdn = "cn=" . $user->UGoonID . "," . Config::get('goonauth.ldapDN');
				$forumdn = "cn=" . $org->GOLDAPGroup . "," . Config::get('goonauth.ldapGroupDN');
				if (!ldap_mod_add($ldap, $forumdn, array('members' => $userdn)))
					error_log("[ldap] Failed to add user to organization group.");
			});

			// Create note about the authorization.
			if (!empty($ntauth))
			{
				NoteHelper::Add(array(
					'user' => $gameuser->user,
					'createdby' => $auth,
					'obj' => $org,
					'type' => $ntauth,
					'text' => "User accepted into organization ".$org->GOName.".",
				));
			}

			// Assemble the data required for the e-mail.
			$maildata = [];
			$maildata = array_add($maildata, 'goonid', $user->UGoonID);
			$maildata = array_add($maildata, 'game', $game->GName);

			// Send out the onboarding e-mail.
			try {
			Mail::send('emails.game-register-approve', $maildata, function($msg) use($game, $user) {
				$msg->subject('Your ' . $game->GName . ' game membership request was approved!');
				$msg->to($user->UEmail);
			});
			} catch (Exception $e) {}
		}
		else
		{
			$rejected = UserStatus::where('USCode', 'REJE')->first();
			$gameuser->gameorgs()->updateExistingPivot($org->GOID,
				array('USID' => $rejected->USID), false);

			// Create note about the authorization.
			if (!empty($ntauth))
			{
				NoteHelper::Add(array(
					'user' => $gameuser->user,
					'createdby' => $auth,
					'obj' => $org,
					'type' => $ntauth,
					'text' => 'User rejected from joining organization '.$org->GOName.'.',
				));
			}

			$maildata = [];
			$maildata = array_add($maildata, 'game', $game->GName);
			$maildata = array_add($maildata, 'reason', 'You suck');

			// Send out the rejection e-mail.
			try {
			Mail::send('emails.game-register-deny', $maildata, function($msg) use($game, $user) {
				$msg->subject('Your ' . $game->GName . ' game membership request was DENIED!');
				$msg->to($user->UEmail);
			});
			} catch (Exception $e) {}
		}

		return Response::json(array(
			'success' => true
		));
	}
}
