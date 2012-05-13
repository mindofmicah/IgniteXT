<?php
function autoload_mock($class)
{
	$class = strtolower($class);
	$parts = explode('\\', $class);
	if ($parts[0] != 'mocks') return;
	
	$filename = array_pop($parts);
	
	$path = implode('/', $parts);
	$path = str_replace('..', '.', $path);
	
	if (file_exists(TESTDIR . $path . '/' . $filename . '.php'))
	{
		include TESTDIR . $path . '/' . $filename . '.php';
		return;
	}
}
spl_autoload_register('autoload_mock');