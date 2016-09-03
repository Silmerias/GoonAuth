<?php

namespace Modules\Games;
use App\Extensions\Modules\GameModule;

use Carbon\Carbon;

class MWO extends GameModule
{
	public function memberVerify($username, $token)
	{
		// Don't bother checking for MWO.
		// Old code left for posterity.
		return true;

		// We have to grab two pages to get to the user profile.
		// The first page is a member search so we can get the profile link.

		$ret = array(
			'ret' => false,
			'userid' => 0,
			'regdate' => Carbon::now(),
			'postcount' => 0,
		);

		// Search for our member.
		$page = @file_get_contents('http://mwomercs.com/forums/index.php?app=members&module=list&name_box=begins&name=' . urlencode($username));
		if ($page === FALSE)
			return $ret;

		$pattern404 = "/<title>.*404.*<\/title>/is";
		$patternProfile = "/<strong><a href='(.+?)' title='View Profile'>(.+?)<\/a><\/strong>/is";

		// Test for a 404.  A 404 means that they are not an RSI user.
		// The 404 should have occurred with file_get_contents, so this is just a sanity check.
		if (preg_match($pattern404, $page) === 1)
			return $ret;

		// See if the profile was found.
		if (preg_match($patternProfile, $page, $matches) !== 1)
			return $ret;

		// Compare names from the profile.
		if (strcasecmp($matches[2], $username) !== 0)
			return $ret;

		// Grab the profile page.
		$profileURL = $matches[1];
		$page = @file_get_contents($profileURL);
		if ($page === FALSE)
			return $ret;

		// See if the token was found.
		if (stristr($page, $token) === false)
			return $ret;

		// We are good at this point.
		$ret['ret'] = true;

		$patternUserID = "/mwomercs\.com\/forums\/user\/(\d+)-.+\//is";
		$patternRegDate = "/Member Since (\d+ \w+ \d+)<br \/>/is";
		$patternPostCount = "/Active Posts.*?<span class='row_data'>([\d,]+).*?<\/span>/is";

		// Grab our user id.
		if (preg_match($patternUserID, $page, $matches) === 1)
			$ret['userid'] = intval($matches[1]);

		// Grab our reg date.
		if (preg_match($patternRegDate, $page, $matches) === 1)
			$ret['regdate'] = Carbon::createFromFormat('j M Y', $matches[1]);

		// Grab our post count.
		if (preg_match($patternPostCount, $page, $matches) === 1)
		{
			$s = str_replace(",", "", $matches[1]);
			$ret['postcount'] = intval($s);
		}

		return $ret;
	}
}
