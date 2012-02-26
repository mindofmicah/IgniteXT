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

namespace System;

function autoload_source($class)
{
	$valid_types = array('models', 'controllers', 'libraries');

	$class = strtolower($class);
	$parts = explode('\\', $class);
	
	$type = array_shift($parts);
	if (!in_array($type, $valid_types)) return;

	$filename = array_pop($parts);
	$filename = str_replace('..', '.', $filename);

	$path = implode('/', $parts);
	$path = str_replace('..', '.', $path);
	
	$check_dirs = array(APPDIR, SHRDIR, IXTDIR);
	foreach ($check_dirs as $dir)
	{
		$location = $dir . 'source/' . $type . '/' . $path . '/' . $filename . '.php';
		if (file_exists($location)) { include $location; return; }
	}
}

function autoload_package($class)
{
	$valid_types = array('models', 'controllers', 'libraries');

	$class = strtolower($class);
	$parts = explode('\\', $class);
	
	$type = array_shift($parts);
	if (!in_array($type, $valid_types)) return;

	$filename = array_pop($parts);
	$filename = str_replace('..', '.', $filename);
	
	$package = array_shift($parts);
	$package = str_replace('..', '.', $package);

	$path = implode('/', $parts);
	$path = str_replace('..', '.', $path);
	
	$check_dirs = array(APPDIR, SHRDIR, IXTDIR);
	foreach ($check_dirs as $dir)
	{
		$location = $dir . 'packages/' . $package . '/' . $type . '/' . $path . '/' . $filename . '.php';
		if (file_exists($location)) { include $location; return; }
	}
}

function autoload_system($class)
{
	$class = strtolower($class);
	$parts = explode('\\', $class);
	if ($parts[0] != 'system') return;
	
	$filename = array_pop($parts);
	
	$path = implode('/', $parts);
	$path = str_replace('..', '.', $path);
	
	$check_dirs = array(APPDIR, SHRDIR, IXTDIR);
	foreach ($check_dirs as $dir)
	{
		if (file_exists($dir . $path . '/' . $filename . '.php'))
		{
			include $dir . $path . '/' . $filename . '.php';
			return;
		}
	}
}

spl_autoload_register('\System\autoload_source');
spl_autoload_register('\System\autoload_package');
spl_autoload_register('\System\autoload_system');
