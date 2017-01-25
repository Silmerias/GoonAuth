<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\ClientException;

use Carbon\Carbon;

use Input;
use Log;
use Mail;
use Redirect;
use Response;
use Session;
use View;

use App\Group;
use App\NoteType;
use App\Sponsor;
use App\User;
use App\UserStatus;

use App\Extensions\Notes\NoteHelper;

class RegisterController extends Controller
{
	public function getIndex()
	{
		return view('register.start');
	}

	public function getType()
	{
		return view('register.type');
	}

	public function getGoon()
	{
		return view('register.goon');
	}

	public function getLink()
	{
		$token = uniqid('FART');
		Session::put('token', $token);

		return view('register.link', array('token' => $token));
	}

	public function getReapply()
	{
		return view('register.reapply');
	}

	public function getSponsored()
	{
		return view('register.sponsored');
	}

	public function getAffiliate()
	{
		return view('register.affiliate');
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
		$user = User::join('UserStatus', 'UserStatus.USID', '=', 'User.USID')
			->where('UGoonID', $goonid)->first();

		// User doesn't exist.
		if (empty($user))
			return 0;

		// Check to see if they were rejected or banned.
		$rej = UserStatus::rejected();
		$ban = UserStatus::banned();
		if ($user->USID == $rej->USID || $user->USID == $ban->USID)
			return -4;

		// Exists and aren't rejected.
		return -3;
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
		$user = User::join('UserStatus', 'UserStatus.USID', '=', 'User.USID')
			->where('UEmail', $email)->first();

		// User doesn't exist.
		if (empty($user))
			return 0;

		// Check to see if they were rejected.
		$rej = UserStatus::rejected();
		if ($user->USID == $rej->USID)
			return -4;

		// Exists and aren't rejected.
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

			// If we are reapplying, allow an in use error to go through.
			if (Input::has('reapply'))
			{
				switch ($valid)
				{
					case -4: return Response::json(array('valid' => 'true', 'message' => 'Goon ID available for reapplication.'));
					default: return Response::json(array('valid' => 'false', 'message' => 'Goon ID is not valid for reapplication.'));
				}
			}

			switch ($valid)
			{
				case 0: return Response::json(array('valid' => 'true', 'message' => 'Goon ID is available.'));

				default:
				case -1: return Response::json(array('valid' => 'false', 'message' => 'Invalid Goon ID.'));
				case -2: return Response::json(array('valid' => 'false', 'message' => 'Your Goon ID may only contain alpha-numeric, underscore, and hyphen characters.'));
				case -3: return Response::json(array('valid' => 'false', 'message' => 'That Goon ID is already in use.'));
				case -4: return Response::json(array('valid' => 'false', 'message' => 'That Goon ID is already in use. Are you trying to <a href="reapply">reapply</a>?'));
			}
		}

		if (Input::has('email'))
		{
			$valid = $this->verifyEmail(Input::get('email'));

			// If we are reapplying, allow an in use error to go through.
			if (Input::has('reapply') && $valid === -4)
				$valid = 0;

			switch ($valid)
			{
				case 0: return Response::json(array('valid' => 'true', 'message' => 'E-mail is acceptable.'));

				default:
				case -1: return Response::json(array('valid' => 'false', 'message' => 'Invalid email.'));
				case -2: return Response::json(array('valid' => 'false', 'message' => 'This is not a valid e-mail address.  Try again.'));
				case -3: return Response::json(array('valid' => 'false', 'message' => 'That e-mail is already in use.'));
				case -4: return Response::json(array('valid' => 'false', 'message' => 'That e-mail is already in use. Are you trying to <a href="reapply">reapply</a>?'));
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

		return redirect()->to('register/link');
	}

	public function postReapply()
	{
		$goonid = $this->verifyGoonID(Input::get('goonid'));
		if ($goonid !== -4)
			return Redirect::back()->with('error', 'There was a problem with your entered GoonID');

		$email = $this->verifyEmail(Input::get('email'));
		if ($email !== 0 && $email !== -4)
			return Redirect::back()->with('error', 'There was a problem with your entered email');

		$user = $this->reapplyUser(Input::get('email'), Input::get('goonid'), null, null, null, null, Input::get('comment'));
		if ($user === null)
			return Redirect::back()->with('error', 'There was an unexpected error. Contact Adeptus for support.');

		return view('register.complete');
	}

	public function postSponsored()
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

		return view('register.complete', array('sponsored' => true));
	}

	public function postLink()
	{
		$goonid = Session::get('register-goonid');
		$email = Session::get('register-email');
		$token = Session::get('token');
		$sa_name = Input::get('sa_username');

		if (!isset($goonid) || !isset($email) || !isset($token))
			return redirect()->to('register')->with('error', 'An unknown error has occured.');

		if (!isset($sa_name))
			return Redirect::back()->with('error', 'You must enter your SA Username.');

		if (User::where('USACachedName', $sa_name)->count() !== 0)
			return Redirect::back()->with('error', 'That SA Username has already been registered.');

		$sa_userid = null;
		$sa_regdate = null;
		$sa_postcount = null;

		// Do verification.
		$verified = !config('goonauth.sa.verify');
		if ($verified === false)
		{
			$cookieJar = new CookieJar();

			$bbpCookie = new SetCookie();
			$bbpCookie->setName('bbpassword');
			$bbpCookie->setDomain('.forums.somethingawful.com');
			$bbpCookie->setValue(config('goonauth.sa.bbpassword'));

			$bbidCookie = new SetCookie();
			$bbidCookie->setName('bbuserid');
			$bbidCookie->setDomain('.forums.somethingawful.com');
			$bbidCookie->setValue(config('goonauth.sa.bbuserid'));

			$cookieJar->setCookie($bbpCookie);
			$cookieJar->setCookie($bbidCookie);

			$client = new Client([
				'base_uri' => 'https://forums.somethingawful.com/',
				'allow_redirects' => false,
				'cookies' => $cookieJar,
			]);

			$body = '';
			try
			{
				$response = $client->request('GET', 'member.php', ['query' => ['action' => 'getinfo', 'username' => $sa_name]]);
				$code = $response->getStatusCode();
				if ($code !== 200)
					throw new \RuntimeException('SA member profile page did not return HTTP status 200.  Returned: '.$code);

				$body = $response->getBody();

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
			}
			catch (Exception $e)
			{
				return Redirect::back()->with('error', 'An unknown error has occured. Please try again.');
			}

			try
			{
				// Check if we have the token.
				if (stristr($body, $token) !== false/* && $sa_regdate < $subTime*/)
					$verified = true;
			}
			catch (Exception $e)
			{
				Log::error(str_repeat('-', 40));
				Log::error($e);
				return Redirect::back()->with('error', 'An unknown error has occured. Please try again.');
			}
		}

		// Check if we succeeded.
		if ($verified === true)
		{
			$comment = Input::get('comment');
			if (!isset($comment) || empty($comment) || strlen($comment) == 0)
				$comment = '';

			$this->createUser($email, $goonid, $sa_userid, $sa_regdate, $sa_name, $sa_postcount, $comment);

			return view('register.complete');
		}
		else
		{
			// Session::flash('error', 'Could not find token in profile or you have not been a member for at least 90 days.');
			return Redirect::back()->with('error', 'Could not find the token in your profile.');
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

		// Save our values.
		$user = new User;
		$user->USID = UserStatus::pending()->USID;
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
			throw $e;
		}

		return $user;
	}

	private function reapplyUser($email, $goonid, $sa_userid, $sa_regdate, $sa_name, $sa_postcount, $comment)
	{
		// Grab IP.
		$ip = inet_pton($_SERVER['REMOTE_ADDR']);
		if ($ip === false)
			$ip = null;
		else $ip = bin2hex($ip);

		// Grab our user
		$user = User::where('UGoonID', $goonid)->first();
		if (empty($user))
			return null;

		// Get our old status so we can save it in the comment.
		$oldstatus = UserStatus::find($user->USID);
		$group = Group::find($user->UGroup);

		// Save our values.
		$user->USID = UserStatus::pending()->USID;
		$user->UIPAddress = $ip;
		$user->UEmail = $email;
		$user->save();

		// Fix comment just in case.
		if (!isset($comment) || empty($comment) || strlen($comment) == 0)
			$comment = '';

		// Create a registration note.
		$reg = NoteType::where('NTCode', 'SYS')->first();
		if (!empty($reg))
		{
			NoteHelper::Add(array(
				'user' => $user,
				'createdby' => null,
				'group' => $group,
				'type' => $reg,
				'subject' => $group->GRName.' reapplication',
				'message' => $comment,
			));
		}

		// Create the status change note.
		$stat = NoteType::where('NTCode', 'STAT')->first();
		if (!empty($stat))
		{
			NoteHelper::Add(array(
				'user' => $user,
				'createdby' => null,
				'group' => $group,
				'type' => $stat,
				'subject' => 'Reapplication',
				'message' => 'Old status: '.$oldstatus->USStatus."\n".'New status: '.UserStatus::pending()->USStatus,
			));
		}

		// Send out the registration e-mail.
		try
		{
			$maildata = [];
			Mail::send('emails.register-complete', $maildata, function($msg) use($user) {
				$msg->subject('Your Goonrathi / Word of Lowtax reapplication request has been submitted for review.');
				$msg->to($user->UEmail);
			});
		}
		catch (Exception $e)
		{
			throw $e;
		}

		return $user;
	}
}
