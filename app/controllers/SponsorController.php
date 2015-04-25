<?php

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

class SponsorController extends BaseController
{
	public function showSponsors()
	{
		$auth = Session::get('auth');

		$include = array('auth' => $auth);
		return View::make('sponsor.list', $include);
	}

	public function showAdd()
	{
		$auth = Session::get('auth');

		$include = array('auth' => $auth);
		return View::make('sponsor.add', $include);
	}

	public function doAdd()
	{
		$auth = Session::get('auth');

		if (!$auth->canSponsor()) {
			Session::flash('error', 'You have exceeded your sponsor limit.');
			return Redirect::back();
		}

		$exists = Sponsor::where('UID', $auth->UID)->whereNull('SSponsoredID')->count();
		if (!empty($exists) && $exists > 3)
		{
			Session::flash('error', 'You have too many unused codes.');
			return Redirect::back();
		}

		$code = substr(uniqid(), 0, 10);
		$sponsor = new Sponsor;
		$sponsor->UID = $auth->UID;
		$sponsor->SCode = $code;
		$sponsor->save();

		return Redirect::back();
	}

}
