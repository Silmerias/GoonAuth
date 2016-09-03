<?php

class StarCitizenGame extends GameModule
{
	public function memberAdded($gameuser)
	{
	}

	public function memberRejected($gameuser)
	{
	}

	public function memberKicked($gameuser)
	{
	}

	public function memberVerify($username, $token)
	{
		// RSI splits important info into two separate profiles.  WTF guys.
		// We first get the site profile which contains our user id and regdate.
		// We then get the forum profile which contains the postcount, separated into posts and threads.  Because why.

		$ret = array(
			'ret' => false,
			'userid' => 0,
			'regdate' => Carbon::now(),
			'postcount' => 0,
		);

		// Grab our site profile.
		$page = FALSE;
		try
		{
			$page = file_get_contents('https://robertsspaceindustries.com/citizens/' . urlencode($username));
		}
		catch (Exception $e)
		{}

		// Failure.  Was probably a 404.  Abort now.
		if ($page === FALSE)
			return $ret;

		$pattern404 = "/<title>.*404.*<\/title>/is";
		$patternUserID = "/UEE Citizen Record.*?<strong class=\"value\">#([\w\s,]+)<\/strong>/is";
		$patternEnlisted = "/Enlisted.*?<strong class=\"value\">([\w\s,]+)<\/strong>/is";

		// Test for a 404.  A 404 means that they are not an RSI user.
		// The 404 should have occurred with file_get_contents, so this is just a sanity check.
		if (preg_match($pattern404, $page) === 1)
			return $ret;

		// See if the token was found.
		if (stristr($page, $token) === false)
			return $ret;

		// We are good at this point.
		$ret['ret'] = true;

		// Grab our user ID.
		if (preg_match($patternUserID, $page, $matches) === 1)
			$ret['userid'] = intval($matches[1]);

		// Grab regdate.
		if (preg_match($patternEnlisted, $page, $matches) === 1)
			$ret['regdate'] = Carbon::createFromFormat('M j, Y', $matches[1]);

		// Grab the forum profile now so we can get our post count.
		$page = FALSE;
		try
		{
			$page = file_get_contents('https://forums.robertsspaceindustries.com/profile/' . urlencode($username));
		}
		catch (Exception $e)
		{}

		// Failure.  Was probably a 404.  Abort now.
		if ($page === FALSE)
			return $ret;

		$patternComments = "/Comments.*?<span class=\"Count\">([\w\s,]+)<\/span>/is";
		$patternDiscussions = "/Discussions.*?<span class=\"Count\">([\w\s,]+)<\/span>.*?<span class=\"Count\">([\w\s,]+)<\/span>/is";

		// Grab our comment count.
		if (preg_match($patternComments, $page, $matches) === 1)
			$ret['postcount'] += intval($matches[1]);

		// Grab our discussion count.
		if (preg_match($patternDiscussions, $page, $matches) === 1)
			$ret['postcount'] += intval($matches[1]);

		return $ret;
	}

	public function memberProfile($gameuser)
	{
		$profile = sprintf($this->game->GProfileURL, $gameuser->GUCachedName);
		$profile = '<a href="'.$profile.'">'.e($gameuser->GUCachedName).'</a>';
		return $profile;
	}
}
