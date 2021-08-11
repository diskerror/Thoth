#!/usr/bin/env php -e
<?php

ini_set('display_errors', '1');
error_reporting(E_ERROR);

require 'vendor/autoload.php';

try {
	$cli = new Service\Cli(__DIR__);
	$cli->init();
	$cli->run($argv);
}
catch (Phalcon\Cli\Dispatcher\Exception $e) {
	fwrite(STDERR, $e->getMessage() . PHP_EOL);
}
catch (Throwable $t) {
	fwrite(STDERR, $t . PHP_EOL);
}
