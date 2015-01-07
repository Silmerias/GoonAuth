<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	if (Session::has('authenticated')) {
		$auth = User::find(Session::get('userid'));
		Session::put('auth', $auth);
	}
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('secure', function()
{
	if (App::environment('production') && !Request::secure())
		return Redirect::secure(Request::path());
});

Route::filter('auth', function()
{
	if (!Session::has('authenticated'))
		return Redirect::to('login');

	if (empty(Session::has('auth')))
		return Redirect::to('login');

	if (Session::get('auth')->userstatus()->banned()->count()) {
		Session::flush();
		return Redirect::to('login')->with('banned', 1);
	}
});

Route::filter('admin', function() {
	/*
	if (!Session::has('auth') || !Session::get('auth')->is_admin)
		return Redirect::to('/');
	*/
});

Route::filter('groupadmin', function($route) {
	$auth = Session::get('auth');
	$grid = $route->getParameter('grid');
	if (!isset($grid))
		return Redirect::to('/');

	if ($auth->grouproles()->where('GRID', $grid)->count() == 0)
		return Redirect::to('/');
});

Route::filter('gameadmin', function($route) {
	$auth = Session::get('auth');
	$abbr = $route->getParameter('abbr');
	$org = $route->getParameter('org');
	if (!isset($abbr) || !isset($org))
		return Redirect::to('games');

	$gameorg = GameOrg::where('GOAbbr', $org)->first();
	if ($auth->gameorgroles()->where('GOID', $gameorg->GOID)->count() == 0)
		return Redirect::to('games/'.$abbr.'/'.$org);
});

Route::filter('sponsor', function() {
	if (!Session::has('auth') || is_null(Session::get('auth')->USAUserID))
		return Redirect::to('/');
});


/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
