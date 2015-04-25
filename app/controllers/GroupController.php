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
			return Response::json(array('success' => false, 'message' => 'Invalid data, please relog and try again.'));

		$auth = Session::get('auth');
		$ntstatus = NoteType::where('NTCode', 'STAT')->first();

		// Check for auth permission.
		$perms = new UserPerm($auth);
		if ($perms->group()->auth == false)
		{
			return Response::json(array(
				'success' => false,
				'message' => 'You do not have permission to authorize members.'
			));
		}

		// Check to see if the user is already active.
		$active = UserStatus::where('USCode', 'ACTI')->first();
		if ($user->USID === $active->USID)
		{
			return Response::json(array(
				'success' => false,
				'message' => 'User is already active in a group.'
			));
		}

		$maildata = array();

		if (strcasecmp($action, "approve") == 0)
		{
			$user->USID = $active->USID;
			$user->save();

			$password = sha1('dick' . intval(rand()) . 'butt');
			$password = substr($password, 0, 13);

			LDAP::Execute(function($ldap) use($password, $user, $group)
			{
				$uid = intval(Config::get('goonauth.ldapUidStart'));
				$gid = intval(Config::get('goonauth.ldapGid'));

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
				$info['homeDirectory'] = '/home/'.$user->UGoonID;
				$info['uidNumber'] = $uid + $user->UID;
				$info['gidNumber'] = $gid;
				$info['objectClass'][0] = "person";
				$info['objectClass'][1] = "inetOrgPerson";
				$info['objectClass'][2] = "posixAccount";

				// Add the user!
				$userdn = "cn=" . $user->UGoonID . "," . Config::get('goonauth.ldapDN');
				if (@ldap_add($ldap, $userdn, $info) == false)
				{
					if (@ldap_modify($ldap, $userdn, $info) == false)
					{
						error_log("[ldap] Failed to create user {$info['cn']}");
						return Response::json(array(
							'success' => false,
							'message' => 'Unable to create user.  Contact Adeptus for assistance.'
						));
					}
				}

				// Delete the user from the group first, just in case.
				@ldap_mod_del($ldap, $forumdn, array('member' => $userdn));

				// Add the user to the forums LDAP group.
				$forumdn = "cn=ForumMembers" . "," . Config::get('goonauth.ldapGroupDN');
				if (@ldap_mod_add($ldap, $forumdn, array('member' => $userdn)) == false)
				{
					error_log("[ldap] Failed to add user to forum group.");
					return Response::json(array(
						'success' => false,
						'message' => 'Unable to add user to forum group.  Contact Adeptus for assistance.'
					));
				}

				// Add the user to the LDAP group for their group.
				if (!is_null($group->GRLDAPGroup))
				{
					$forumdn = "cn=". $group->GRLDAPGroup . "," . Config::get('goonauth.ldapGroupDN');

					// Delete first.
					@ldap_mod_del($ldap, $forumdn, array('member' => $userdn));

					// Add to the LDAP group.
					if (@ldap_mod_add($ldap, $forumdn, array('member' => $userdn)) == false)
					{
						error_log("[ldap] Failed to add user to group: {$group->GRLDAPGroup}.");
						return Response::json(array(
							'success' => false,
							'message' => 'Unable to add user to group.  Contact Adeptus for assistance.'
						));
					}
				}
			});

			// Send out the onboarding e-mail.
			try
			{
				// Assemble the data required for the e-mail.
				$maildata = array_add($maildata, 'goonid', $user->UGoonID);
				$maildata = array_add($maildata, 'group', $user->group->GRName);
				$maildata = array_add($maildata, 'password', $password);

				Mail::send('emails.group-register-approve', $maildata, function($msg) use($user) {
					$msg->subject('Your Goonrathi membership request was approved!');
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

			// Create note about the authorization.
			if (!empty($ntstatus))
			{
				NoteHelper::Add(array(
					'user' => $user,
					'createdby' => $auth,
					'group' => $group,
					'type' => $ntstatus,
					'subject' => 'Authorization',
					'message' => 'User accepted into group '.$group->GRName.'.',
				));
			}

			// Connect to IPB to create the forum entry.
			file_get_contents("https://forums.goonrathi.com/index.php?app=core&module=global&section=login&do=process&auth_key=880ea6a14ea49e853634fbdc5015a024&ips_username={$user->UGoonID}&ips_password={$password}");
		}
		else
		{
			$reason = Input::get('text');

			// Try sending the e-mail out first.
			// If this fails, we want to stop the process until it can be fixed.
			try
			{
				$maildata = array_add($maildata, 'reason', $reason);

				Mail::send('emails.group-register-deny', $maildata, function($msg) use($user) {
					$msg->subject('Your Goonrathi membership request was DENIED!');
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
			$user->USID = $rejected->USID;
			$user->save();

			// Create note about the rejection.
			if (!empty($ntstatus))
			{
				NoteHelper::Add(array(
					'user' => $user,
					'createdby' => $auth,
					'group' => $group,
					'type' => $ntstatus,
					'subject' => 'Authorization',
					'message' => 'User rejected from joining group '.$group->GRName.".\nReason: ".$reason,
				));
			}
		}

		return Response::json(array(
			'success' => true
		));
	}

	public function showGroupMembers($grid)
	{
		$auth = Session::get('auth');
		$group = Group::find($grid);
		if (empty($group))
			return Redirect::to('/');

		$rejected = UserStatus::where('USCode', 'REJE')->first();
		$members = $group->members();

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

		if (Input::has('sa'))
		{
			$get['sa'] = Input::get('sa');
			$get['sa-by'] = Input::get('sa-by');

			$sa = Input::get('sa');
			$by = Input::get('sa-by', 'contains');
			if (strcmp($by, 'starts') === 0) $by = "$sa%";
			else $by = "%$sa%";

			$members = $members->where('User.USACachedName', 'LIKE', $by);
		}

		if (Input::has('status'))
		{
			$get['status'] = Input::get('status');

			$statuses = explode(',', Input::get('status'));
			$members = $members->whereIn('User.USID', $statuses);
		}
		else $members = $members->where('User.USID', '<>', $rejected->USID);

		if (Input::has('regdate'))
		{
			$sys = NoteType::where('NTCode', 'SYS')->first();
			$get['regdate'] = Input::get('regdate');
			$get['regdate-by'] = Input::get('regdate-by');

			$compare = '>=';
			if ($get['regdate-by'] == 'lte') $compare = '<=';
			if ($get['regdate-by'] == 'e') $compare = '=';

			$members = $members->join('Note', function($j) use($sys, $compare, $get)
			{
				$j->on('Note.UID', '=', 'User.UID')
					->where('Note.NTID', '=', $sys->NTID)
					->where('Note.NSubject', 'like', '%registration%')
					->where('Note.NTimestamp', $compare, $get['regdate']);
			});
		}

		if (Input::has('orderby'))
		{
			$order = Input::get('orderby');
			if ($order == 'goonid') $members = $members->orderBy('UGoonID');
			if ($order == 'status') $members = $members->orderBy('USID');
			if ($order == 'regdate')
			{
				if (!Input::has('regdate'))
				{
					$sys = NoteType::where('NTCode', 'SYS')->first();
					$members = $members->join('Note', function($j) use($sys)
					{
						$j->on('Note.UID', '=', 'User.UID')
							->where('Note.NTID', '=', $sys->NTID)
							->where('Note.NSubject', 'like', '%registration%');
					});
				}
				$members = $members->orderBy('NTimestamp', 'DESC');
			}
		}
		else $members = $members->orderBy('UGoonID');

		// Distinct.
		$members = $members->distinct();

		// Paginate!
		$members = $members->paginate(15);

		// Make sure we include our GET parameters.
		if (count($get) !== 0)
			$members = $members->appends($get);

		$include = array('auth' => $auth, 'group' => $group, 'members' => $members);
		return View::make('group.viewmembers', $include);
	}

	public function doGroupMembers($grid)
	{
		$auth = Session::get('auth');
		$group = Group::find($grid);
		if (empty($group))
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
						'group' => ($global == true ? null : $group),
						'type' => $type,
						'subject' => $subject,
						'message' => $message,
					));
				}

				return Response::json(array('success' => true));
		}

		return Response::json(array('success' => false));
	}
}
