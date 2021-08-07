<?php

namespace Thoth\Service\StdIo;

use Thoth\Service\Exception\RuntimeException;

function in(int $length = 80)
{
	if (($in = fread(STDIN, $length)) === false) {
		throw new RuntimeException('fread from STDIN returned false');
	}

	return $in;
}

function jsonOut($o)
{
	outln(json_encode($o, JSON_PRETTY_PRINT));
}

function outln($s = '')
{
	out($s . PHP_EOL);
}

function out($s)
{
	if (fwrite(STDOUT, $s) === false) {
		throw new RuntimeException('fwrite to STDOUT returned false');
	}
}

function err($s)
{
	if (fwrite(STDERR, $s . PHP_EOL) === false) {
		throw new RuntimeException('fwrite to STDERR returned false');
	}
}

/**
 * The goal is to have this output look like a modernized "var_export" without class types,
 *      just the data structure.
 * Four things are changed:
 * 1) Objects are changed to an array;
 * 2) Usage of array() is changed to [];
 * 3) Indexes numbers are removed from indexed arrays that are contiguous and start at zero;
 * 4) Change the formatting from 2 to 4 spaces per tab.
 *
 * (For best results we should build our own "var_export" from scratch.)
 *
 * @param $o
 */
function phpOut($o)
{
	if (is_object($o)) {
		$o = (method_exists($o, 'toArray')) ?
			$o->toArray() :
			(array) $o;
	}

	$out = var_export($o, true);
	$out = preg_replace(
		['/array\s+\(/s', '/^(\s+)\),/m', '/=>\s+\[/s', '/\)$/', '/^(  +)/m'],
		['[', '$1],', '=> [', ']', '$1$1'],
		$out
	);

	/**
	 * Remove numbered keys when keys start at zero.
	 * (This only works when there are no nested indexed arrays.)
	 */
	$arr = explode("\n", $out);
	$i   = 0;
	$cnt = 0;
	foreach ($arr as &$a) {
		if (preg_match('/  +0 => /', $a) === 1) {
			$i = 0;
		}

		$a = preg_replace('/(  +)' . $i . ' => /', '$1', $a, 1, $cnt);
		$i += $cnt;
	}

	out(implode(PHP_EOL, $arr));
}
