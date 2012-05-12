<?php
/**
 *==============================================================================*
 *  ,--,                                   ,--,                                 *
 *  |  |                                   |  |            ,--,  ,------------, *
 *  |  |                          ,--, ,---'  '---,        |  |  |  ,--,  ,---' *
 *  |  |                          '__' '---,  ,---'         \  \/  /   |  |     *
 *  |  |    ,-----,   ,------,    ,--,     |  |   ,------,   \    /    |  |     *
 *  |  |   /  _   |  |  ,--,  |   |  |     |  |  |   ==  |   /    \    |  |     *
 *  |  |  |  / \  |  |  |  |  |   |  |     |  |  |  ,----'  /  /\  \   |  |     *
 *  |  |  |  \_/  |  |  |  |  |   |  |     |  |  |  '---', |  |  |  |  |  |     *
 * '----'  \      | '----''----' '----'   '----'  '-----,' '--'  '--' '----'    *
 *          '--|  |                                                             *
 *     ,'------'  '     P H P   F R A M E W O R K                               *
 *     ',---------'                                                             *
 *==============================================================================*
 *
 * @copyright  Copyright 2011-2012, Website Duck LLC (http://www.websiteduck.com)
 * @link       http://www.ignitext.com IgniteXT PHP Framework
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

define('BASEDIR', dirname(__FILE__) . '/');

/**
 * Load the application configuration file. 
 */
require 'json_config.php';
$mode = 'development';
$application_config = \System\Classes\JSON_Config::read('config.json', $mode);
if ($application_config === false) throw new Exception('Failed to load configuration file.');

/**
 * Create constants using data from the config file.
 */
define('APPID', $application_config['general']['APPID']);
define('APPDIR', $application_config['directories']['APPDIR']);
define('SHRDIR', $application_config['directories']['SHRDIR']);
define('IXTDIR', $application_config['directories']['IXTDIR']);
define('BASEURL', $application_config['general']['BASEURL']);
define('ASSETS', 
	($application_config['general']['ASSETS_PREPEND_BASEURL'] ? BASEURL : '') . 
	$application_config['general']['ASSETS']
);


/**
 * Find and require the autoloader. 
 */
if (file_exists(APPDIR . 'system/autoload.php')) require APPDIR . 'system/autoload.php';
elseif (file_exists(SHRDIR . 'system/autoload.php')) require SHRDIR . 'system/autoload.php';
elseif (file_exists(IXTDIR . 'system/autoload.php')) require IXTDIR . 'system/autoload.php';
else throw new Exception('Autoloader not found.');

\System\Profiler::start();
session_start();

/**
 * Load all of the config files. 
 */
$dirs = array(IXTDIR, SHRDIR, APPDIR);
foreach ($dirs as $dir) foreach (glob($dir . 'config/*.php') as $config_file) include $config_file;

/**
 * Run the application by starting the route which will call the appropriate controller.
 */
\System\Profiler::event(\System\Event_Type::NORMAL, 'IgniteXT', 'Start Application', 'Application has started running.');
\System\Router::route(isset($_GET['ixt_route']) ? $_GET['ixt_route'] : '');
\System\Profiler::event(\System\Event_Type::NORMAL, 'IgniteXT', 'Finish Application', 'Application has finished running.');