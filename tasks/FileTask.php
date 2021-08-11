<?php

class FileTask extends TaskMaster
{
	/**
	 * Compare file basenames under two paths.
	 */
	function NameCompareAction()
	{
		$pathsToIgnore = file($this->_basePath . '/data/dotNetIgnore.txt');
	}

	/**
	 * Get size of all _files under path.
	 */
	function SizeAction()
	{
	}
}
