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

	public function getGoonSponsored()
	{
		return View::make('register.goon-sponsored');
	}

	public function getAffiliate()
	{
		return View::make('register.affiliate');
	}

	private function verifyGoonID($goonid)
	{
		if (!isset($goonid) || strlen($goonid) == 0)
			return -1;

		// Check for valid characters.
		$pattern = '/[^A-Za-z0-9_-]/';
		if (preg_match($pattern, $goonid) === 1)
			return -2;

		// See of the GoonID exists.
		$count = User::join('UserStatus', 'UserStatus.USID', '=', 'User.USID')
			->where('UGoonID', $goonid)
			->where('USCode', '<>', 'REJE')
			->count();

		if ($count !== 0)
			return -3;

		return 0;
	}

	private function verifyEmail($email)
	{
		if (!isset($email) || strlen($email) == 0)
			return -1;

		// Hahahahaha
		// http://stackoverflow.com/a/1917982
		// RFC 5322 compliant.
		$pattern = '/(?(DEFINE)
			(?<address>         (?&mailbox) | (?&group))
			(?<mailbox>         (?&name_addr) | (?&addr_spec))
			(?<name_addr>       (?&display_name)? (?&angle_addr))
			(?<angle_addr>      (?&CFWS)? < (?&addr_spec) > (?&CFWS)?)
			(?<group>           (?&display_name) : (?:(?&mailbox_list) | (?&CFWS))? ;
			                                      (?&CFWS)?)
			(?<display_name>    (?&phrase))
			(?<mailbox_list>    (?&mailbox) (?: , (?&mailbox))*)

			(?<addr_spec>       (?&local_part) \@ (?&domain))
			(?<local_part>      (?&dot_atom) | (?&quoted_string))
			(?<domain>          (?&dot_atom) | (?&domain_literal))
			(?<domain_literal>  (?&CFWS)? \[ (?: (?&FWS)? (?&dcontent))* (?&FWS)?
			                             \] (?&CFWS)?)
			(?<dcontent>        (?&dtext) | (?&quoted_pair))
			(?<dtext>           (?&NO_WS_CTL) | [\x21-\x5a\x5e-\x7e])

			(?<atext>           (?&ALPHA) | (?&DIGIT) | [!#\$%&\'*+-\\/=?^_`{|}~])
			(?<atom>            (?&CFWS)? (?&atext)+ (?&CFWS)?)
			(?<dot_atom>        (?&CFWS)? (?&dot_atom_text) (?&CFWS)?)
			(?<dot_atom_text>   (?&atext)+ (?: \. (?&atext)+)*)

			(?<text>            [\x01-\x09\x0b\x0c\x0e-\x7f])
			(?<quoted_pair>     \\ (?&text))

			(?<qtext>           (?&NO_WS_CTL) | [\x21\x23-\x5b\x5d-\x7e])
			(?<qcontent>        (?&qtext) | (?&quoted_pair))
			(?<quoted_string>   (?&CFWS)? (?&DQUOTE) (?:(?&FWS)? (?&qcontent))*
			                    (?&FWS)? (?&DQUOTE) (?&CFWS)?)

			(?<word>            (?&atom) | (?&quoted_string))
			(?<phrase>          (?&word)+)

			# Folding white space
			(?<FWS>             (?: (?&WSP)* (?&CRLF))? (?&WSP)+)
			(?<ctext>           (?&NO_WS_CTL) | [\x21-\x27\x2a-\x5b\x5d-\x7e])
			(?<ccontent>        (?&ctext) | (?&quoted_pair) | (?&comment))
			(?<comment>         \( (?: (?&FWS)? (?&ccontent))* (?&FWS)? \) )
			(?<CFWS>            (?: (?&FWS)? (?&comment))*
			                   (?: (?:(?&FWS)? (?&comment)) | (?&FWS)))

			# No whitespace control
			(?<NO_WS_CTL>       [\x01-\x08\x0b\x0c\x0e-\x1f\x7f])

			(?<ALPHA>           [A-Za-z])
			(?<DIGIT>           [0-9])
			(?<CRLF>            \x0d \x0a)
			(?<DQUOTE>          ")
			(?<WSP>             [\x20\x09])
			)

			(?&address)/x';

		// Check for a valid e-mail address.
		if (preg_match($pattern, $email) !== 1)
			return -2;

		// See of the email exists.
		$count = User::join('UserStatus', 'UserStatus.USID', '=', 'User.USID')
			->where('UEmail', $email)
			->where('USCode', '<>', 'REJE')
			->count();

		if ($count !== 0)
			return -3;

		return 0;
	}

	private function verifyCode($code)
	{
		if (!isset($code) || strlen($code) == 0)
			return -1;

		// Check for an existing code.
		$sponsor = Sponsor::where('SCode', $code)->whereNull('SSponsoredID')->first();
		if (empty($sponsor))
			return -2;

		// Return our sponsor name.
		return $sponsor->user->UGoonID;
	}

	public function postCheck()
	{
		if (Input::has('goonid'))
		{
			$valid = $this->verifyGoonID(Input::get('goonid'));
			switch ($valid)
			{
				case 0: return Response::json(array('valid' => 'true', 'message' => 'Goon ID is available.'));

				default:
				case -1: return Response::json(array('valid' => 'false', 'message' => 'Invalid GoonID.'));
				case -2: return Response::json(array('valid' => 'false', 'message' => 'Your GoonID may only contain alpha-numeric, underscore, and hyphen characters.'));
				case -3: return Response::json(array('valid' => 'false', 'message' => 'That GoonID is already in use.'));
			}
		}

		if (Input::has('email'))
		{
			$valid = $this->verifyEmail(Input::get('email'));
			switch ($valid)
			{
				case 0: return Response::json(array('valid' => 'true', 'message' => 'E-mail is acceptable.'));

				default:
				case -1: return Response::json(array('valid' => 'false', 'message' => 'Invalid email.'));
				case -2: return Response::json(array('valid' => 'false', 'message' => 'This is not a valid e-mail address.  Try again.'));
				case -3: return Response::json(array('valid' => 'false', 'message' => 'That e-mail is already in use.'));
			}
		}

		if (Input::has('code'))
		{
			$valid = $this->verifyCode(Input::get('code'));
			switch ($valid)
			{
				default: return Response::json(array('valid' => 'true', 'message' => 'You are being sponsored by <strong>'.$valid.'</strong>.'));

				case -1:
				case -2: return Response::json(array('valid' => 'false', 'message' => 'Invalid sponsor code.'));
			}
		}
	}

	public function postGoon()
	{
		$goonid = $this->verifyGoonID(Input::get('goonid'));
		if ($goonid !== 0)
			return Redirect::back()->with('error', 'There was a problem with your entered GoonID');

		$email = $this->verifyEmail(Input::get('email'));
		if ($email !== 0)
			return Redirect::back()->with('error', 'There was a problem with your entered email');

		Session::put('register-goonid', Input::get('goonid'));
		Session::put('register-email', Input::get('email'));

		$token = uniqid('FART');
		Session::put('token', $token);

		return View::make('register.link', array('token' => $token));
	}

	public function postGoonSponsored()
	{
		$goonid = $this->verifyGoonID(Input::get('goonid'));
		if ($goonid !== 0)
			return Redirect::back()->with('error', 'There was a problem with your entered GoonID.');

		$email = $this->verifyEmail(Input::get('email'));
		if ($email !== 0)
			return Redirect::back()->with('error', 'There was a problem with your entered email.');

		$code = $this->verifyCode(Input::get('code'));
		if ($code === -1 || $code === -2)
			return Redirect::back()->with('error', 'There was a problem with your sponsor code.');

		$comment = 'Sponsored by: '.$code;
		$user = $this->createUser(Input::get('email'), Input::get('goonid'), null, null, null, null, $comment);

		$sponsor = Sponsor::where('SCode', Input::get('code'))->first();
		$sponsor->sponsored()->associate($user);
		$sponsor->save();

		return View::make('register.complete', array('sponsored' => true));
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

		if (User::where('USACachedName', $sa_name)->count() !== 0)
			return Redirect::back()->with('error', 'That SA Username has already been registered.');

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
				$comment = Input::get('comment');
				if (!isset($comment) || empty($comment) || strlen($comment) == 0)
					$comment = '';

				$this->createUser($email, $goonid, $sa_userid, $sa_regdate, $sa_name, $sa_postcount, $comment);

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

	private function createUser($email, $goonid, $sa_userid, $sa_regdate, $sa_name, $sa_postcount, $comment)
	{
		$group = Group::where('GRCode', 'SA')->first();

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
		$user->USARegDate = $sa_regdate !== null ? Carbon::createFromFormat('M j, Y', $sa_regdate) : null;
		$user->USACachedName = $sa_name;
		$user->USACachedPostCount = $sa_postcount;
		$user->USACacheDate = Carbon::now();
		$user->UGroup = $group->GRID;
		$user->save();

		// Create a note.
		$reg = NoteType::where('NTCode', 'SYS')->first();
		if (!empty($reg))
		{
			NoteHelper::Add(array(
				'user' => $user,
				'createdby' => null,
				'group' => $group,
				'type' => $reg,
				'subject' => $group->GRName.' registration',
				'message' => $comment,
			));
		}

		// Send out the registration e-mail.
		try
		{
			$maildata = [];
			Mail::send('emails.register-complete', $maildata, function($msg) use($user) {
				$msg->subject('Your Goonrathi / Word of Lowtax membership request has been submitted for review.');
				$msg->to($user->UEmail);
			});
		}
		catch (Exception $e)
		{
			error_log('E-mail error: '.$e->getMessage());
		}

		return $user;
	}
}
