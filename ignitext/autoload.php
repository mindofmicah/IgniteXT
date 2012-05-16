<?php
/**
 * Autoloader
 * 
 * Automatically loads a PHP file that contains the class requested by your
 * application.
 *
 * @copyright  Copyright 2011-2012, Website Duck LLC (http://www.websiteduck.com)
 * @link       http://www.ignitext.com IgniteXT PHP Framework
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace System\Services;

function autoload_source($class)
{
	$valid_types = array('models', 'controllers', 'services', 'entities');

	$class = strtolower($class);
	$parts = explode('\\', $class);
	
	$type = array_shift($parts);
	if (!in_array($type, $valid_types)) return;

	$filename = array_pop($parts);

	$path = implode('/', $parts);
	
	$check_dirs = array(APPDIR, SHRDIR, IXTDIR);
	foreach ($check_dirs as $dir)
	{
		for ($i = 0; $i <= count($parts); $i++)
		{
			$location = $dir . 'source/';
			if ($i > 0) $location .= implode(array_slice($parts, 0, $i),'/') . '/';
			if ($i > 0 && !is_dir($location)) continue 2; //If this isn't a directory, none of the others will be either
			$location .= $type . '/';
			if ($i < count($parts)) $location .= implode(array_slice($parts, -(count($parts)-$i)),'/') . '/';
			$location .= $filename . '.php';
			if (file_exists($location)) { include $location; return; }
		}
	}
}

function autoload_package($class)
{
	$valid_types = array('models', 'controllers', 'services', 'entities');

	$class = strtolower($class);
	$parts = explode('\\', $class);
	
	$type = array_shift($parts);
	if (!in_array($type, $valid_types)) return;

	$filename = array_pop($parts);
	$package = array_shift($parts);

	$path = implode('/', $parts);
	
	$check_dirs = array(APPDIR, SHRDIR, IXTDIR);
	foreach ($check_dirs as $dir)
	{
		$location = $dir . 'packages/' . $package . '/' . $type . '/' . $path . '/' . $filename . '.php';
		if (file_exists($location)) { include $location; return; }
		$location = $dir . 'packages/' . $package . '/' . $path . '/' . $type . '/' . $filename . '.php';
		if (file_exists($location)) { include $location; return; }
	}
}

spl_autoload_register('\System\Services\autoload_source');
