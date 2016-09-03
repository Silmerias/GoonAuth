<?php

return [

	'enabled' => env('LDAP_ENABLED', true),
	'host' => env('LDAP_HOST', 'localhost'),
	'port' => env('LDAP_PORT', 389),

	'admin' => [
		'user' => env('LDAP_ADMIN', ''),
		'password' => env('LDAP_ADMINPWD', ''),
	],

	'dn' => [
		'users' => env('LDAP_DN_USERS', ''),
		'groups' => env('LDAP_DN_GROUPS', ''),
	],

	'uid_start' => env('LDAP_UID', 20000),
	'gid_start' => env('LDAP_GID', 10000),

	'default_group' => env('LDAP_DEFAULT_GROUP', ''),

];
