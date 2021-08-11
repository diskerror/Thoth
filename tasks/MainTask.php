<?php

use Service\Reflector;
use function Service\StdIo\outln;

class MainTask extends TaskMaster
{
	/**
	 * Display list of commands, subcommands, and config structure.
	 */
	public function mainAction()
	{
		outln(
			'Usage: [./]' . $this->config->process->name . '[.php] [command [sub-command] [arguments...]]'
		);
		outln();

		foreach (glob(__DIR__ . '/*Task.php') as $fileName) {
			$className = basename($fileName, '.php');

			//	Skip this file.
			if ($className === 'MainTask') {
				continue;
			}

			$cmd = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', substr($className, 0, -4)));
			outln("Command:\n\t" . $cmd . PHP_EOL);

			outln('Sub-commands:');
			$refl = new Reflector($className);
			foreach ($refl->getFormattedDescriptions() as $description) {
				outln("\t" . $description);
			}
			outln();
		}
	}
}
