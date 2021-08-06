<?php

/**
 * All nested arrays are converted to nested Phalcon\Config objects.
 *
 * To add to or override these values
 * create another file in this directory
 * that ends in '.php' with contents like:
 *
 * return [
 *        'twitter'    => [
 *            'auth' => [
 *                'consumer_key'       => 'wwww',
 *                'consumer_secret'    => 'xxxx',
 *                'oauth_token'        => 'yyyy',
 *                'oauth_token_secret' => 'zzzz',
 *            ],
 *        ],
 *    ];
 */

return [

	//	Name of the user's configuration file.
	'userConfigName' => '.thoth.php',

	'version' => '0.1',

	'process' => [
		'name'    => 'thoth',
		'path'    => '/var/run/',
		'procDir' => '/proc/'    //	location of actual PID
	],

	'cache' => [
		'index' => [
			'front' => [
				'lifetime' => 600,    //	ten minutes
				'adapter'  => 'data',
			],
			'back'  => [
				'dir'      => '/run/shm/thoth/',
				'prefix'   => 'index',
				'frontend' => null,
				'adapter'  => 'file',
			],
		],

		'tag_cloud' => [
			'front' => [
				'lifetime' => 2,    //	two seconds
				'adapter'  => 'data',
			],
			'back'  => [
				'dir'      => '/run/shm/thoth/',
				'prefix'   => 'tag_cloud',
				'frontend' => null,
				'adapter'  => 'file',
			],
		],

		'summary' => [
			'front' => [
				'lifetime' => 6,    //	six seconds
				'adapter'  => 'data',
			],
			'back'  => [
				'dir'      => '/run/shm/thoth/',
				'prefix'   => 'summary',
				'frontend' => null,
				'adapter'  => 'file',
			],
		],

	],

];
