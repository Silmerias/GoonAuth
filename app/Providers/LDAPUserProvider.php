<?php

use \Illuminate\Auth\UserInterface;
use \Illuminate\Auth\UserProviderInterface;

class LDAPUserProvider implements UserProviderInterface
{
	public function retrieveById($identifier)
	{
		return User::find($identifier);
	}

	public function retrieveByToken($identifier, $token)
	{
		return User::where('UID', $identifier)->where('URememberToken', $token)->first();
	}

	public function updateRememberToken(UserInterface $user, $token)
	{
		$user->URememberToken = $token;
		$user->save();
	}

	public function retrieveByCredentials(array $credentials)
	{
		return User::where('UGoonID', $credentials['username'])->first();
	}

	public function validateCredentials(UserInterface $user, array $credentials)
	{
		return LDAP::PasswordCheck($user->UGoonID, $credentials['password']);
	}
}
