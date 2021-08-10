#!/usr/bin/env php
<?php

//ini_set('display_errors', '0');
//error_reporting(E_ERROR);

require 'vendor/autoload.php';

try {
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
