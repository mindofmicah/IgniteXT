<?php
/**
 * Library
 * 
 * The base class for a library.
 *
 * @copyright  Copyright 2011-2012, Website Duck LLC (http://www.websiteduck.com)
 * @link       http://www.ignitext.com IgniteXT PHP Framework
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace System\Classes;

class Library {
	private function log($event_type, $description)
	{
		\System\Profiler::event($event_type, __NAMESPACE__.__CLASS__, __METHOD__, $description);
	}
	
	private function is_logging()
	{
		\System\Profiler::is_logging(__NAMESPACE__.__CLASS__);
	}
}