<?php

namespace Thoth\Service;


use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher\Exception;
use Phalcon\Di\FactoryDefault\Cli as FdCli;
use Phalcon\Events\Manager;
use Thoth\Resource\PidHandler;
use Thoth\Service\Exception\RuntimeException;
use Thoth\Service\StdIo;
use Thoth\Structure\Config;

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

		$di->setShared('logger', function() use ($self) {
			static $logger;
			if (!isset($logger)) {
//				$logger = LoggerFactory::getFileLogger($self->_basePath . '/' . $config->process->name . '.log');
				$logger = LoggerFactory::getStreamLogger();
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
			// Parse CLI arguments.
			//	CLI options will be parsed into $config later.
			$args = [];
			if (array_key_exists(1, $argv)) {
				$args['task'] = $argv[1];

				if (array_key_exists(2, $argv)) {
					$args['action'] = $argv[2];

					if (array_key_exists(3, $argv)) {
						$args['params'][] = $argv[3];
					}
				}
			}

			$this->_application->handle($args);
		}
		catch (Exception $e) {
			$message = $e->getMessage();
			if (($pos = strpos($message, 'Task handler class cannot be loaded')) !== false) {
				StdIo::err(substr($message, 0, $pos) . ' command does not exist.');
			}
			else {
				throw $e;
			}
		}

		return PHP_EOL;
	}
}
