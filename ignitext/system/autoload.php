<?php

/**
 * Includes a PHP file that should contain the requested class.
 * 
 * @param string $class
 */
function ignitext_autoload($class)
{
	$valid_folders = array('system', 'models', 'controllers', 'libraries');

	$class = strtolower($class);
	$parts = explode('\\', $class);
	
	if (in_array($parts[0], $valid_folders) == false) return;

	$filename = array_pop($parts);
	
	if ($parts[0] == 'models' || $parts[0] == 'controllers') 
	{
		$parts[] = array_shift($parts);
		array_unshift($parts,'source');
	}

	$path = implode('/', $parts);
	$path = str_replace('..', '.', $path);
	
	$check_dirs = array(APPDIR, SHRDIR, IXTDIR);
	foreach ($check_dirs as $dir)
	{
		if (file_exists($dir . $path . '/' . $filename . '.php'))
		{
			include $dir . $path . '/' . $filename . '.php';
			break;
		}
	}
}

spl_autoload_register('ignitext_autoload');
