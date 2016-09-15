<?php

return [

	'version' => '2.1.20160914',

	/**
	 * Your SA bbuserid and bbpassword cookie values. Required to auth accounts.
	 */
	'sa' => [
		'verify' => env('SA_VERIFY', true),
		'bbuserid' => env('SA_BBUSERID', 0),
		'bbpassword' => env('SA_BBPASSWORD', 'fffffffffffffffff'),
	],

	/**
	 * URL to your forums
	 */
	'forum_url' => 'http://forums.goonrathi.com/',

	/**
	 * Sponsorship configuration.
	 */
	'sponsor' => [
		'allow' => true,
		'max' => null,
	],

	/**
	 * For the FLJK org_module.
	 */
	'rsi' => [
		'verify' => env('RSI_VERIFY', false),
		'user' => env('RSI_USER', ''),
		'password' => env('RSI_PASSWORD', ''),
	],

];
