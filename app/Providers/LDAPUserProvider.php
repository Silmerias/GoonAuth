<?php

namespace App\Providers;

use App\User;
use App\Extensions\LDAP\LDAP;

use \Illuminate\Contracts\Auth;
use Illuminate\Support\ServiceProvider as ServiceProvider;

class LDAPUserProvider extends ServiceProvider implements Auth\UserProvider
{
    public function register()
    {
        //
    }

	public function retrieveById($identifier)
	{
		return User::find($identifier);
	}

	public function retrieveByToken($identifier, $token)
	{
		return User::where('UID', $identifier)->where('URememberToken', $token)->first();
	}

	public function updateRememberToken(Auth\Authenticatable $user, $token)
	{
		$user->URememberToken = $token;
		$user->save();
	}

	public function retrieveByCredentials(array $credentials)
	{
		return User::where('UGoonID', $credentials['username'])->first();
	}

	public function validateCredentials(Auth\Authenticatable $user, array $credentials)
	{
		return LDAP::PasswordCheck($user->UGoonID, $credentials['password']);
	}
}
