<?php

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookiePlugin;

class UserController extends BaseController
{

	public function showHome()
	{
		$auth = Session::get('auth');

		$include = array('auth' => $auth);
		return View::make('user.home', $include);
	}

	public function showLink()
	{
		$auth = Session::get('auth');
		if (isset($auth) && !empty($auth->sa_username)) {
			return Redirect::to('/');
		}

		$token = AuthToken::where('xf_id', $auth->xf_id)->first();

		if (empty($token)) {
			$token = new AuthToken();
			$token->xf_id = $auth->xf_id;
			$token->token = uniqid(Config::get('goonauth.codePrefix').":");
			$token->save();
		}

		$include = array("token" => $token->token);

		return View::make('user.link', $include);
	}

	public function doLink()
	{
		$auth = Session::get('auth');
		if (isset($auth) && !empty($auth->sa_username)) {
			return Redirect::to('/');
		}

		$token = AuthToken::where('xf_id', $auth->xf_id)->first();

		if (empty($token)) {
			return Redirect::back();
		}

		$blacklist = Blacklist::where('sa_username', Input::get('sa_username'))->count();

		if ($blacklist > 0) {
			return Redirect::back();
		}

		$user = User::where('sa_username', '=', Input::get('sa_username'))->count();

		if ($user > 0) {
			return Redirect::back();
		}

		$cookieJar = new ArrayCookieJar();

	    $bbpCookie = new Cookie();
	    $bbpCookie->setName('bbpassword');
	    $bbpCookie->setDomain('.forums.somethingawful.com');
	    $bbpCookie->setValue(Config::get('goonauth.bbpassword'));

	    $bbidCookie = new Cookie();
	    $bbidCookie->setName('bbuserid');
	    $bbidCookie->setDomain('.forums.somethingawful.com');
	    $bbidCookie->setValue(Config::get('goonauth.bbuserid'));

	    $cookieJar->add($bbpCookie);
	    $cookieJar->add($bbidCookie);

	    $cookiePlugin = new CookiePlugin($cookieJar);

	    $client = new Client('http://forums.somethingawful.com/member.php', array(
	        'request.options' => array(
	            'query' => array(
	                'action' => 'getinfo',
	                'username' => Input::get('sa_username'),
	            ),
	        ),
	    ));
	    $client->addSubscriber($cookiePlugin);
	    $request = $client->get(null, array(), array('allow_redirects' => false));



	    try {
	        $response = $request->send();
	        $body = $response->getBody(true);

	        preg_match("/<dt>Member Since<\/dt>([\s]+)?<dd>([A-Za-z,0-9\s]+)<\/dd>/i", $body, $matches);

	        $memberTime = strtotime($matches[2]);

	        $subTime = time() - (60 * 60 * 24 * 90);

	        if (stristr($body, $token->token) !== false && $memberTime < $subTime) {
	            $client = new Client(Config::get('goonauth.apiUrl'), array(
	                'request.options' => array(
	                    'query' => array(
	                        'action' => 'editUser',
	                        'hash' => Config::get('goonauth.apiKey'),
	                        'group' => Config::get('goonauth.authId'),
	                        'user' => Session::get('displayUsername'),
	                    ),
	                ),
	            ));

	            $request = $client->get();
	            $auth->sa_username = Input::get('sa_username');
	            $auth->linked_at = Carbon::now();

	            try {
	                $response = $request->send();

	                $auth->save();
	            } catch (ClientErrorResponseException $ex) {
	                $json = $ex->getResponse()->json();

	                // ignore
	                if ($json['error'] === 7) {
	                	$auth->save();

	                    return Redirect::to('/');
	                }

	                Session::flash('error', 'Unknown error occured. Please try again.');

	                return Redirect::back();
	            }

	            return Redirect::to('/');
	        } else {
	            Session::flash('error', 'Could not find token in profile or you have not been a member for at least 90 days.');

	            return Redirect::back();
	        }
	    } catch (ClientErrorResponseException $ex) {
	        Session::flash('error', 'Unknown error occured. Please try again.');

	        return Redirect::back();
	    }
	}

	public function doLogin()
	{
		$valid = $this->LDAPPasswordCheck(Input::get('goonid'), Input::get('password'));
		if ($valid === false)
			return Redirect::back()->with('error', 'Invalid username/password.');

		$user = User::where('UGoonID', Input::get('goonid'))->first();
		if (!isset($user))
			return Redirect::back()->with('error', 'This user is not in our system.');

		Session::put('authenticated', true);
		Session::put('userid', $user->UID);
		Session::put('displayUsername', Input::get('goonid'));

		return Redirect::to('/');
	}


	private function LDAPExecute( $func )
	{
		$ldaphost = Config::get('goonauth.ldapHost');
		$ldapport = Config::get('goonauth.ldapPort');

		// Connect to our LDAP server.
		$ldap = ldap_connect($ldaphost, $ldapport);
		if (!$ldap)
		{
			error_log("[ldapsync] Could not connect to $ldaphost");
			return;
		}

		// Set options.
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

		// Grab some settings.
		$ldapuser = Config::get('goonauth.ldapUser');
		$ldappass = Config::get('goonauth.ldapPassword');

		// If nothing was entered, use NULL so we do an anonymous bind.
		if (strlen($ldapuser) === 0)
			$ldapuser = NULL;
		else $ldapuser = "cn=" . $ldapuser . "," . Config::get('goonauth.ldapDN');;
		if (strlen($ldappass) === 0)
			$ldappass = NULL;

		// Attempt to bind now.
		if (ldap_bind($ldap, $ldapuser, $ldappass))
		{
			// Execute our function.
			$func($ldap);
		}
		else
		{
			error_log("[ldapsync] Failed to bind to $ldaphost : $ldapport with user $ldapuser");
		}

		ldap_close($ldap);
	}

	private function LDAPPasswordCheck($username, $password)
	{
		return true;
		
		if (!isset($username) || !isset($password) || strlen($password) == 0)
			return false;

		$ldaphost = Config::get('goonauth.ldapHost');
		$ldapport = Config::get('goonauth.ldapPort');

		// Connect to our LDAP server.
		$ldap = ldap_connect($ldaphost, $ldapport);
		if (!$ldap)
		{
			error_log("[ldapsync] Could not connect to $ldaphost");
			return false;
		}

		// Set options.
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

		// Grab some settings.
		$ldapuser = "cn=" . $username . "," . Config::get('goonauth.ldapDN');
		$ldappass = $password;

		// Attempt to bind now.
		$ret = false;
		try
		{
			if (ldap_bind($ldap, $ldapuser, $ldappass))
				$ret = true;
		}
		catch (Exception $e) {}

		ldap_close($ldap);
		return $ret;
	}
}
