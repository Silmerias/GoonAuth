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

	public function showAddForm()
	{
		$auth = Session::get('auth');

		$include = array('auth' => $auth);
		return View::make('sponsor.add', $include);
	}

	public function doAddSponsor()
	{
		$auth = Session::get('auth');

		if ($auth->sponsors->count() >= Config::get('goonauth.sponsors')) {
			Session::flash('error', 'You have exceeded your sponsor limit.');
			return Redirect::back();
		}

	    $client = new Client(Config::get('goonauth.apiUrl'), array(
	        'request.options' => array(
	            'query' => array(
	                'action' => 'getUser',
	                'hash' => Config::get('goonauth.apiKey'),
	                'value' => Input::get('username'),
	            ),
	        ),
	    ));

	    $request = $client->get();

	    try {
	        $response = $request->send();
	        $json = $response->json();

	        if (!isset($json['user_state']) || $json['user_state'] !== 'valid') {
	            Session::flash('error', 'This user has not validated their email account.');

	            return Redirect::back();
	        }
	    } catch (ClientErrorResponseException $ex) {
	        Session::flash('error', 'Unknown error occurred. Please try again.');

	        return Redirect::back();
	    }

	    if ($json['user_group_id'] == 5) {
	    	Session::flash('error', 'This account is already authenticated.');
	    	return Redirect::back();
	    }

	    $sponsor = User::where('xf_id', $json['user_id'])->first();

	    if (empty($sponsor)) {
	    	$sponsor = new User();
	    	$sponsor->xf_id = $json['user_id'];
	    	$sponsor->xf_username = $json['username'];
	    }
    	$sponsor->is_sponsored = true;
    	$sponsor->save();

	    $auth->sponsors()->attach($sponsor->id);

	    $client = new Client(Config::get('goonauth.apiUrl'), array(
            'request.options' => array(
                'query' => array(
                    'action' => 'editUser',
                    'hash' => Config::get('goonauth.apiKey'),
                    'group' => Config::get('goonauth.sponsorId'),
                    'user' => $sponsor->xf_username,
                ),
            ),
        ));

        $request = $client->get();
        $sponsor->linked_at = Carbon::now();

        try {
            $response = $request->send();

            $sponsor->save();
        } catch (ClientErrorResponseException $ex) {
            $json = $ex->getResponse()->json();

            // ignore
            if ($json['error'] === 7) {
            	$sponsor->save();

                return Redirect::to('sponsors');
            }

            Session::flash('error', 'Unknown error occured. Please try again.');

            return Redirect::back();
        }

	    return Redirect::to('sponsors');
	}

}
