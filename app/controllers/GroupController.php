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

	public function doAuth($grid)
	{
		$action = Input::get('action');
		$uid = Input::get('id');
		$group = Group::find($grid);
		$user = User::find($uid);

		// Test for valid user.
		if (empty($user))
			return Response::json(array('success' => false));

		$auth = Session::get('auth');
		$ntauth = NoteType::where('NTCode', 'STAT')->first();

		$maildata = array();

		if (strcasecmp($action, "approve") == 0)
		{
			$active = UserStatus::where('USCode', 'ACTI')->first();
			$user->USID = $active->USID;
			$user->save();

			$password = sha1('dick' . intval(rand()) . 'butt');
			$password = substr($password, 0, 13);

			LDAP::Execute(function($ldap) use($password, $user, $group) {
				// Generate a salt.
				$salt = md5(uniqid(rand(), TRUE));
				$salt = substr($salt, 0, 4);

				// Generate the new password hash.
				$pass = html_entity_decode($password);
				$pass = "{SSHA}" . base64_encode(pack("H*", sha1($pass . $salt)) . $salt);

				// Record the data for our new entry.
				$info['cn'] = $user->UGoonID;
				$info['sn'] = $user->UGoonID;
				$info['uid'] = $user->UGoonID;
				$info['displayName'] = $user->UGoonID;
				$info['userPassword'] = $pass;
				$info['mail'] = $user->UEmail;
				$info['objectClass'][0] = "person";
				$info['objectClass'][1] = "inetOrgPerson";

				// Add the user!
				$userdn = "cn=" . $user->UGoonID . "," . Config::get('goonauth.ldapDN');
				if (!ldap_add($ldap, $userdn, $info))
					error_log("[ldap] Failed to create user {$info['cn']}");

				// Add the user to the forums LDAP group.
				$forumdn = "cn=ForumMembers" . "," . Config::get('goonauth.ldapGroupDN');
				if (!ldap_mod_add($ldap, $forumdn, array('members' => $userdn)))
					error_log("[ldap] Failed to add user to forum group.");

				// Add the user to the LDAP group for their group.
				if (!is_null($group->GRLDAPGroup))
				{
					$forumdn = "cn=". $group->GRLDAPGroup . "," . Config::get('goonauth.ldapGroupDN');
					if (!ldap_mod_add($ldap, $forumdn, array('members' => $userdn)))
						error_log("[ldap] Failed to add user to forum group.");
				}
			});

			// Create note about the authorization.
			if (!empty($ntauth))
			{
				NoteHelper::Add(array(
					'user' => $user,
					'createdby' => $auth,
					'obj' => $group,
					'type' => $ntauth,
					'text' => "User accepted into group ".$group->GRName.".",
				));
			}

			// Connect to IPB to create the forum entry.
			file_get_contents("https://forums.goonrathi.com/index.php?app=core&module=global&section=login&do=process&auth_key=880ea6a14ea49e853634fbdc5015a024&ips_username={$user->UGoonID}&ips_password={$password}");

			// Assemble the data required for the e-mail.
			$maildata = array_add($maildata, 'goonid', $user->UGoonID);
			$maildata = array_add($maildata, 'group', $user->group->GRName);
			$maildata = array_add($maildata, 'password', $password);

			// Send out the onboarding e-mail.
			try {
			Mail::send('emails.group-register-approve', $maildata, function($msg) {
				$msg->subject('Your Goonrathi / Word of Lowtax membership request was approved!');
				$msg->to($user->UEmail);
			});
			} catch (Exception $e) {}
		}
		else
		{
			$reason = Input::get('text');

			$rejected = UserStatus::where('USCode', 'REJE')->first();
			$user->USID = $rejected->USID;
			$user->save();

			// Create note about the authorization.
			if (!empty($ntauth))
			{
				NoteHelper::Add(array(
					'user' => $user,
					'createdby' => $auth,
					'obj' => $group,
					'type' => $ntauth,
					'text' => 'User rejected from joining group '.$group->GRName.'.  Reason: '.$reason,
				));
			}

			$maildata = array_add($maildata, 'reason', $reason);

			try {
			Mail::send('emails.group-register-deny', $maildata, function($msg) {
				$msg->subject('Your Goonrathi / Word of Lowtax membership request was DENIED!');
				$msg->to($user->UEmail);
			});
			} catch (Exception $e) {}
		}

		return Response::json(array(
			'success' => true
		));
	}
}
