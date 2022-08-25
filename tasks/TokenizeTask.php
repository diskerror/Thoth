<?php

use Service\StdIo;

/**
 * Tokenize code.
 */
class TokenizeTask extends TaskMaster
{
	public function main()
	{
		$code = file_get_contents(func_get_arg(0));
		StdIo::jsonOut(token_get_all($code));
	}
}
