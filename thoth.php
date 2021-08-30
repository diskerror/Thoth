#!/usr/bin/env php
<?php

//ini_set('display_errors', '0');
error_reporting(E_ERROR);

try {
	require 'vendor/diskerror/autoload/autoload.php';

	$cli = new Service\Cli(__DIR__);
	$cli->run($argv);
}
catch (Throwable $t) {
	fwrite(STDERR, $t . PHP_EOL);
	exit(1);
}
