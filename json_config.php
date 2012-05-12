<?php
/**
 * JSON Config Reader
 * 
 * Turns json config file into an array of objects.  
 *
 * @copyright  Copyright 2011-2012, Website Duck LLC (http://www.websiteduck.com)
 * @link       http://www.ignitext.com IgniteXT PHP Framework
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace System\Classes;

abstract class JSON_Config
{
	public static function read($filename, $mode)
	{
		if (!file_exists($filename)) return false;
		$config_input = file_get_contents($filename);
		$config_input = preg_replace('~^\s*//.*$~m', '', $config_input);
		$config = json_decode($config_input, true);
		foreach ($config as $config_mode => $config_obj)
		{
			if (!empty($config_obj['inherits']))
			{
				$inherit_arr = explode(',', $config_obj['inherits']);
				foreach ($inherit_arr as $inherit)
				{
					$config[$config_mode] = array_merge_recursive($config[$inherit], $config[$config_mode]);
				}
			}
		}
		return $config[$mode];
	}
}