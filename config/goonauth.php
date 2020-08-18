<?php

return [

	'version' => '2.2.20200816',

	/**
	 * The default admin account for GoonAuth.
	 */
	'admin' => env('GOONAUTH_ADMIN', 'admin'),

	/**
	 * Your SA bbuserid and bbpassword cookie values. Required to auth accounts.
	 */
	'sa' => [
		'verify' => env('SA_VERIFY', true),
		'bbuserid' => env('SA_BBUSERID', 0),
		'bbpassword' => env('SA_BBPASSWORD', 'fffffffffffffffff'),
	],

	/**
	 * URLS
	 */
	'forum_url' => env('FORUM_URL', ''),
	'rules_url' => env('RULES_URL', ''),
	'wiki_url' => env('WIKI_URL', ''),
	'discord_api' => env('DISCORD_ENDAPI', ''),
	'bot_api' => env('DISCORD_BOTAPI', ''),

	/**
	 * Sponsorship configuration.
	 */
	'sponsor' => [
		'allow' => env('SPONSOR', false),
		'max' => env('SPONSOR_MAX', null),
	],

	/**
	 * Discord auth_module.
	 */
	'discord' => env('DISCORD', false),

	/**
	 * Star Citizen game_module.
	 */
	'starcitizen' => env('GAME_STARCITIZEN', false),

	/**
	 * Eve Echoes game_module.
	 */
	'eveechoes' => env('GAME_EVEECHOES', false),

	/**
	 * For the Star Citizen org_module.
	 */
	'rsi' => [
		'verify' => env('RSI_VERIFY', false),
		'user' => env('RSI_USER', ''),
		'password' => env('RSI_PASSWORD', ''),
	],
];
