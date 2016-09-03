<?php

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookiePlugin;

class AdminController extends BaseController
{
	public function showList()
	{
		if (Input::has('search')) {
			$users = User::select('auth.*')
						->where('xf_username', 'LIKE','%'.Input::get('search').'%')
						->orWhere('sa_username', 'LIKE', '%'.Input::get('search').'%')
						->orWhere('characters.name', 'LIKE', '%'.Input::get('search').'%')
						->leftJoin('characters', 'auth.id', '=', 'characters.auth_id')
						->groupBy('auth.id')
						->paginate(20);
		} else {
			$users = User::paginate(20);
		}

		$include = array('users' => $users, 'search' => Input::get('search'));

		return View::make('admin.list', $include);
	}

	public function showUser(User $user)
	{
		if (Input::has('admin') && in_array(Session::get('auth')->id, Config::get('goonauth.superAdmins'))) {
			if (!in_array($user->id, Config::get('goonauth.superAdmins'))) {
				$user->is_admin = Input::get('admin');
				$user->save();
			}
		}

		$include = array('user' => $user);
		return View::make('admin.user', $include);
	}

	public function banUser(User $user)
	{
		if (in_array(Session::get('auth')->id, Config::get('goonauth.superAdmins')) && !$user->is_admin) {
			$user->is_banned = !$user->is_banned;
			$user->save();

			$note = new Note();
			$note->admin_id = Session::get('auth')->id;
			$note->auth_id = $user->id;
			if ($user->is_banned) {
				$note->note = "User Banned";
				$group = Config::get('goonauth.unauthedId');

			} else {
				$note->note = "User Unbanned";

				if ($user->is_sponsored) {
					$group = Config::get('goonauth.sponsorId');
				} else {
					$group = Config::get('goonauth.authId');
				}
			}

			$note->save();

			$client = new Client(Config::get('goonauth.apiUrl'), array(
                'request.options' => array(
                    'query' => array(
                        'action' => 'editUser',
                        'hash' => Config::get('goonauth.apiKey'),
                        'group' => $group,
                        'user' => $user->xf_username,
                    ),
                ),
            ));

            $request = $client->get();

            try {
                $response = $request->send();
            } catch (ClientErrorResponseException $ex) {
                $json = $ex->getResponse()->json();

                // ignore
                if ($json['error'] === 7) {

                    return Redirect::back();
                }

                Session::flash('error', 'Unknown error occured. Please try again.');

                return Redirect::back();
            }
			
			

		}
		return Redirect::back();
	}

	public function lockCharacter(Character $character) {
		$character->locked = !$character->locked;
		$character->save();

		return Redirect::back();
	}
}