<?php

namespace Resource;

use Iterator;
use Resource\Exception\InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Structure\StringArray;
use function current;
use function key;

class Paths implements Iterator
{
	private $_path  = '';
	private $_files = [];

	protected $_pathsToIgnore = [];

	public function __construct(StringArray $paths)
	{
		$this->_pathsToIgnore = $paths->toArray();
	}

	public function loadFileNames(string $path): void
	{
		$this->_path = realpath($path);

		if (!file_exists($this->_path)) {
			throw new InvalidArgumentException('The path "' . $this->_path . '" does not exist.');
		}

		$pathLen = strlen($this->_path) - strlen(basename($this->_path));

		$fileItr = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_path));

		$this->_files = [];
		foreach ($fileItr as $file) {
			$name = (string) $file->getPathname();
			if (!$file->isDir() && !$this->_arrayValueInString($name)) {
				$this->_files[substr($name, $pathLen)] = basename($name);
			}
		}
	}

	protected function _arrayValueInString(string $haystack): bool
	{
		foreach ($this->_pathsToIgnore as $needle) {
			if (strpos($haystack, $needle) !== false) {
				return true;
			}
		}
		return false;
	}

	public function current(): string
	{
		return current($this->_files);
	}

	public function next(): bool
	{
		next($this->_files);
	}

	public function key(): string
	{
		return key($this->_files);
	}

	public function valid(): bool
	{
		return $this->valid($this->_files);
	}

	public function rewind(): void
	{
		$this->rewind($this->_files);
	}

}
