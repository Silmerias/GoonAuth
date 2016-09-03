<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::controller('register', 'RegisterController');

Route::post('login', ['middleware' => 'csrf', 'uses' => 'UserController@doLogin']);
Route::get('login', ['middleware' => 'csrf', function() {
	return View::make('user.login');
}]);

Route::get('logout', function() {
	Auth::logout();
	Session::flush();
	return Redirect::to('login');
});

Route::group(['middleware' => 'auth'], function() {
	Route::get('/', ['uses' => 'UserController@showHome']);

	Route::get('user/{id}', 'UserController@showUser');
	Route::get('user/{id}/notes', 'UserController@showNotes');

	Route::get('games', 'GameController@getRoot');
	Route::get('games/{abbr}', 'GameController@getGames');
	Route::get('games/{abbr}/link', 'GameController@getGamesLink');
	Route::post('games/{abbr}/link', 'GameController@postGamesLink');
	Route::post('games/{abbr}/link/check-user', 'GameController@postGamesLinkCheckUser');

	Route::get('games/{abbr}/{org}', 'GameController@getGamesOrg');
	Route::get('games/{abbr}/{org}/join', 'GameController@getGamesOrgJoin');
	Route::post('games/{abbr}/{org}/join', 'GameController@postGamesOrgJoin');

	Route::get('games/{abbr}/{org}/view', 'GameController@getGamesOrgView');
	Route::post('games/{abbr}/{org}/view', array('before' => 'auth|gameadmin', 'uses' => 'GameController@postGamesOrgView'));

	Route::group(array('before' => 'auth|gameadmin', 'prefix' => 'auth'), function() {
		Route::get('games/{abbr}/{org}', 'GameController@getAuthGamesOrg');
		Route::post('games/{abbr}/{org}', 'GameController@postAuthGamesOrg');
	});

	Route::group(array('before' => 'auth|groupadmin', 'prefix' => 'auth'), function() {
		Route::get('group/{grid}', 'GroupController@showAuth');
		Route::post('group/{grid}', 'GroupController@doAuth');
		Route::get('group/{grid}/view', 'GroupController@showGroupMembers');
		Route::post('group/{grid}/view', 'GroupController@doGroupMembers');
	});

	Route::group(array('before' => 'sponsor'), function() {
		Route::get('sponsor', 'SponsorController@showSponsors');
		Route::get('sponsor/add', 'SponsorController@showAdd');
		Route::post('sponsor/add', 'SponsorController@doAdd');
	});

	Route::group(array('before' => 'auth|admin', 'prefix' => 'admin'), function() {
		Route::model('user', 'User');
		Route::get('/', 'AdminController@showList');
		Route::post('/', 'AdminController@showList');
		Route::get('user/{user}', 'AdminController@showUser');
		Route::get('user/{user}/ban', 'AdminController@banUser');
		Route::resource('note', 'NoteController');
		Route::resource('blacklist', 'BlacklistController');
		Route::get('character/lock/{character}', 'AdminController@lockCharacter');
	});
});
