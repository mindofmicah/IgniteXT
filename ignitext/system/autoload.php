<?php

/**
 * Includes a PHP file that should contain the requested class.
 * 
 * @param string $class
 */
function ignitext_autoload($class)
{
	$valid_folders = array('system', 'models', 'libraries');

	$class = strtolower($class);
	$parts = explode('\\', $class);
	
	if (in_array($parts[0], $valid_folders) == false) return;

	$filename = array_pop($parts);

	$path = implode('/', $parts);
	$path = str_replace('..', '.', $path);

	if (file_exists(APPDIR . $path . '/' . $filename . '.php'))
		include APPDIR . $path . '/' . $filename . '.php';
	elseif (file_exists(SHRDIR . $path . '/' . $filename . '.php'))
		include SHRDIR . $path . '/' . $filename . '.php';					
	elseif (file_exists(IXTDIR . $path . '/' . $filename . '.php'))
		include IXTDIR . $path . '/' . $filename . '.php';				
}

spl_autoload_register('ignitext_autoload');
