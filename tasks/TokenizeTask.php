<?php

use Service\StdIo;

/**
 * Tokenize code.
 */
class TokenizeTask extends TaskMaster
{
	/**
	 * Dump all tokens in named file to screen.
	 *
	 * @param string $fName
	 *
	 * @return void
	 */
	public function getAllAction(string $fName)
	{
		$code = file_get_contents($fName);
		StdIo::jsonOut(token_get_all($code));
	}
}
