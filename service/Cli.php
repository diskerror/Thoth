<?php

namespace Service;


use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;
use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher\Exception;
use Phalcon\Di\FactoryDefault\Cli as FdCli;
use Phalcon\Events\Manager;
use Resource\LoggerFactory;
use Resource\PidHandler;
use Service\Exception\RuntimeException;
use Service\StdIo;
use Structure\Config;
use function var_export;

class Cli
{
	/**
	 * @var string
	 */
	protected $_basePath;

	/**
	 * @var (application)
	 */
	protected $_application;

	/**
	 * DiAbstract constructor.
	 *
	 * @param string $basePath
	 */
	final public function __construct(string $basePath)
	{
		if (!is_dir($basePath)) {
			throw new RuntimeException('"$_basePath" base path does not exist.');
		}

		$this->_basePath = $basePath;
	}

	/**
	 * @return Cli
	 */
	public function init(): self
	{
		$di = new FdCli();

		$self = $this;

		$di->setShared('config', function() use ($self) {
			static $config;

			if (!isset($config)) {
				$configName = $self->_basePath . '/config.php';
				$devName    = $self->_basePath . '/dev.config.php';

				//	File must exist.
				$config = new Config(require $configName);

				if (file_exists($devName)) {
					$config->replace(require $devName);
				}

				$config = new Config($config);

				$userConfigName = getenv('HOME') . '/' . $config->userConfigName;
				if (file_exists($userConfigName)) {
					$config->replace(require $userConfigName);
				}
			}

			return $config;
		});

		$di->setShared('logger', function() use ($self, $di) {
			static $logger;
			if (!isset($logger)) {
				$logger = new LoggerFactory(
					$self->_basePath . '/' . $di->getShared('config')->process->name . '.log'
				);
			}
			return $logger;
		});

		$di->setShared('eventsManager', function() {
			static $eventsManager;
			if (!isset($eventsManager)) {
				$eventsManager = new Manager();
			}
			return $eventsManager;
		});

		$di->setShared('pidHandler', function() use ($di) {
			static $pidHandler;
			if (!isset($pidHandler)) {
				$pidHandler = new PidHandler($di->getShared('config')->process);
			}
			return $pidHandler;
		});


		$this->_application = new Console($di);

		return $this;
	}

	/**
	 * @param array $argv
	 *
	 * @throws Exception
	 */
	public function run(array $argv): string
	{
		try {
			$opts = new OptionCollection();
			$opts->add('v|verbose');
			$parser     = new OptionParser($opts);
			$result     = $parser->parse($argv);
			$parsedArgv = [];
			foreach ($result->arguments as $argument) {
				$parsedArgv[] = $argument->arg;
			}

			// Parse CLI arguments.
			//	CLI options will be parsed into $config later.
			$args = [];
			if (array_key_exists(0, $parsedArgv)) {
				$args['task'] = $parsedArgv[0];

				if (array_key_exists(1, $parsedArgv)) {
					$args['action'] = $parsedArgv[1];

					if (array_key_exists(2, $parsedArgv)) {
						$args['params'][] = $parsedArgv[2];
					}
				}
			}

			$this->_application->handle($args);
		}
		catch (Exception $e) {
			$message = $e->getMessage();
			if (($pos = strpos($message, 'Task handler class cannot be loaded')) !== false) {
				StdIo\err(substr($message, 0, $pos) . ' command does not exist.');
			}
			else {
				throw $e;
			}
		}

		return PHP_EOL;
	}
}
