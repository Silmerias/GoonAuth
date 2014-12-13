<?php

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookiePlugin;

class RegisterController extends BaseController
{
	public function getIndex()
	{
		return View::make('register.list');
	}

	public function getGoon()
	{
		return View::make('register.goon');
	}

	public function getAffiliate()
	{
		return View::make('register.affiliate');
	}

	public function postGoon()
	{
		$count = User::where('UGoonID', Input::get('goonid'))
			->orWhere('UEmail', Input::get('email'))
			->count();

		if ($count !== 0)
		{
			Session::flash('error', 'That username or email has been taken.');
			return Redirect::back();
		}

		Session::put('register-goonid', Input::get('goonid'));
		Session::put('register-email', Input::get('email'));

		$token = uniqid('FART_');
		Session::put('token', $token);

		return View::make('register.link', array('token' => $token));
	}

	public function postLink()
	{
		$goonid = Session::get('register-goonid');
		$email = Session::get('register-email');
		$token = Session::get('token');
		$sa_name = Input::get('sa_username');

		if (!isset($goonid) || !isset($email) || !isset($token))
		{
			Session::flash('error', 'An unknown error has occured.');
			return Redirect::to('register');
		}

		if (!isset($sa_name))
		{
			Session::flash('error', 'You must enter your SA Username.');
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
					'username' => $sa_name,
				),
			),
		));

		$client->addSubscriber($cookiePlugin);
		$request = $client->get(null, array(), array('allow_redirects' => false));

		try
		{
			$response = $request->send();
			$body = $response->getBody(true);

			// User ID
			preg_match("/<input type=\"hidden\" name=\"userid\" value=\"(\d+)\">/i", $body, $matches);
			$sa_userid = intval($matches[1]);

			// Member Since
			preg_match("/<dt>Member Since<\/dt>[\s]*<dd>([A-Za-z,0-9\s]+)<\/dd>/i", $body, $matches);
			$sa_regdate = $matches[1];
			// $subTime = time() - (60 * 60 * 24 * 90);

			// Post Count
			preg_match("/<dt>Post Count<\/dt>[\s]*<dd>(\d+)<\/dd>/i", $body, $matches);
			$sa_postcount = intval($matches[1]);

			// Check if we have the token.
			if (stristr($body, $token) !== false/* && $sa_regdate < $subTime*/)
			{
				// Success!  Let's create our user now.
				$user = new User;
				$user->USID = UserStatus::pending()->first()->USID;
				$user->UEmail = $email;
				$user->UGoonID = $goonid;
				$user->USAUserID = $sa_userid;
				$user->USARegDate = Carbon::createFromFormat('M j, Y', $sa_regdate);
				$user->USACachedName = $sa_name;
				$user->USACachedPostCount = $sa_postcount;
				$user->USACacheDate = Carbon::now();
				$user->save();

				return View::make('register.complete');
			}
			else
			{
				// Session::flash('error', 'Could not find token in profile or you have not been a member for at least 90 days.');
				Session::flash('error', 'Could not find the token in your profile.');
				return Redirect::back();
			}
		}
		catch (ClientErrorResponseException $ex)
		{
			Session::flash('error', 'An unknown error has occured. Please try again.');
			return Redirect::back();
		}
		return Redirect::back();
	}
}
