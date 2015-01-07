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

	public function postCheckGoon()
	{
		$count = $count = User::join('UserStatus', 'UserStatus.USID', '=', 'User.USID')
			->where('UGoonID', Input::get('goonid'))
			->where('USCode', '<>', 'REJE')
			->count();

		return Response::json(array(
			'valid' => ($count === 0 ? 'true' : 'false')
		));
	}

	public function postGoon()
	{
		$count = User::join('UserStatus', 'UserStatus.USID', '=', 'User.USID')
			->where('UGoonID', Input::get('goonid'))
			->where('USCode', '<>', 'REJE')
			->count();

		if ($count !== 0)
			return Redirect::back()->with('error', 'That Goon ID has been taken.');

		$count = User::where('UEmail', Input::get('email'))->count();
		if ($count !== 0)
			return Redirect::back()->with('error', 'That email has been taken.');

		Session::put('register-goonid', Input::get('goonid'));
		Session::put('register-email', Input::get('email'));

		$token = uniqid('FART');
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
			return Redirect::to('register')->with('error', 'An unknown error has occured.');

		if (!isset($sa_name))
			return Redirect::back()->with('error', 'You must enter your SA Username.');

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
				$group = Group::where('GRName', 'Something Awful')->first();

				// Grab IP.
				$ip = inet_pton($_SERVER['REMOTE_ADDR']);
				if ($ip === false)
					$ip = null;
				else $ip = bin2hex($ip);

				// Success!  Let's create our user now.
				$user = new User;
				$user->USID = UserStatus::pending()->first()->USID;
				$user->UIPAddress = $ip;
				$user->UEmail = $email;
				$user->UGoonID = $goonid;
				$user->USAUserID = $sa_userid;
				$user->USARegDate = Carbon::createFromFormat('M j, Y', $sa_regdate);
				$user->USACachedName = $sa_name;
				$user->USACachedPostCount = $sa_postcount;
				$user->USACacheDate = Carbon::now();
				$user->UGroup = $group->GRID;
				$user->save();

				// Create a note if a registration comment was specified.
				$comment = Input::get('comment');
				if (isset($comment) && !empty($comment) && strlen($comment) != 0)
				{
					$reg = NoteType::where('NTCode', 'SYS')->first();
					if (!empty($reg))
					{
						NoteHelper::Add(array(
							'user' => $user,
							'createdby' => null,
							'obj' => $group,
							'type' => $reg,
							'text' => $group->GRName." registration comment:\n".$comment,
						));
					}
				}

				// Send out the registration e-mail.
				try
				{
					Mail::send('emails.register-complete', null, function($msg) use($user) {
						$msg->subject('Your Goonrathi / Word of Lowtax membership request has been submitted for review.');
						$msg->to($user->UEmail);
					});
				}
				catch (Exception $e)
				{
					error_log('E-mail error: '.var_dump($e));
				}

				return View::make('register.complete');
			}
			else
			{
				// Session::flash('error', 'Could not find token in profile or you have not been a member for at least 90 days.');
				return Redirect::back()->with('error', 'Could not find the token in your profile.');
			}
		}
		catch (ClientErrorResponseException $ex)
		{
			return Redirect::back()->with('error', 'An unknown error has occured. Please try again.');
		}
		return Redirect::back();
	}
}
