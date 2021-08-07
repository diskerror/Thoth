<?php

namespace Thoth\Structure;

use Diskerror\Typed\TypedClass;
use Thoth\Structure\Config\Process;

class Config extends TypedClass
{
	protected $version        = '';
	protected $userConfigName = '';
	protected $process        = [Process::class];
}
