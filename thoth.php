#!/usr/bin/env php
<?php

//ini_set('display_errors', '0');
//error_reporting(E_ERROR);

require 'vendor/autoload.php';

try {
	$loader = new Phalcon\Loader();
	$loader->registerDirs([__DIR__ . '/tasks/']);
	$loader->registerNamespaces([
		'Thoth\\Logic'     => __DIR__ . '/logic',
		'Thoth\\Resource'  => __DIR__ . '/resource',
		'Thoth\\Service'   => __DIR__ . '/service',
		'Thoth\\Structure' => __DIR__ . '/struct',
	]);
	$loader->registerFiles([
			'Thoth\\Service\\StdIo' => __DIR__ . '/service/StdIo.php'
	]);
	$loader->register();

	$cli = new Thoth\Service\Cli(__DIR__);
	$cli->init();
	$cli->run($argv);
}
catch (Phalcon\Cli\Dispatcher\Exception $e) {
	fwrite(STDERR, $e->getMessage() . PHP_EOL);
}
catch (Throwable $t) {
	fwrite(STDERR, $t . PHP_EOL);
}
