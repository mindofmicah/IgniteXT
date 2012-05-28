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

namespace Services\System\Classes;

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
					$config[$config_mode] = array_replace_recursive($config[$inherit], $config[$config_mode]);
				}
			}
		}
		array_walk_recursive($config, function(&$value, $key) use ($config) {
			if (strpos($value, '{{') !== false)
			{
				$value = preg_replace_callback("/{{(.*)}}/", function($matches) use ($config) {
					$arr_string = $matches[1];
					$arr_string_arr = explode('.', $arr_string);
					$ref = &$config;
					foreach ($arr_string_arr as $arr_part) 
					{
						if (!isset($ref[$arr_part])) throw new \Exception('Config replacement does not exist: {{' . $arr_string . '}}');
						$ref = &$ref[$arr_part];
					}
					return $ref;
				}, $value);
			}
		});
		
		return $config[$mode];
	}
}