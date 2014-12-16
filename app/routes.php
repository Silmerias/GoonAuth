<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::controller('register', 'RegisterController');

Route::get('/', array('before' => 'auth', 'uses' => 'UserController@showHome'));
Route::get('login', function() {
	if (empty(Session::get('auth')))
		Session::flush();

	return View::make('user.login');
});
Route::post('login', 'UserController@doLogin');
Route::get('logout', function() {
	Session::flush();
	return Redirect::to('login');
});

Route::group(array('before' => 'auth'), function() {
	Route::get('user/{id}', 'UserController@showUser');

	Route::get('games', 'GameController@showGames');
	Route::get('games/{abbr}', 'GameController@showGame');
	Route::get('games/{abbr}/link', 'GameController@showGameLink');
	Route::post('games/{abbr}/link', 'GameController@doGameLink');

	Route::get('games/{abbr}/{org}', 'GameController@showGameOrg');
	Route::post('games/{abbr}/{org}', 'GameController@doGameOrg');

	Route::group(array('before' => 'auth|groupadmin', 'prefix' => 'auth'), function() {
		Route::get('group/{grid}', 'GroupController@showAuth');
		Route::post('group/{grid}', 'GroupController@doAuth');
	});

	Route::group(array('before' => 'auth|gameadmin', 'prefix' => 'auth'), function() {
		Route::get('games/{abbr}', 'GameController@showAuth');
		Route::post('games/{abbr}', 'GameController@doAuth');
	});

	Route::group(array('before' => 'sponsor'), function() {
		Route::get('sponsor', 'SponsorController@showSponsors');
		Route::get('sponsor/add', 'SponsorController@showAddForm');
		Route::post('sponsor/add', 'SponsorController@doAddSponsor');
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
