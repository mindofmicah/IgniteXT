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
$application_config = \Services\System\Classes\JSON_Config::read('config.json', $mode);
if ($application_config === false) throw new Exception('Failed to load configuration file.');

/**
 * Create constants using data from the config file.
 */
define('APPID', $application_config['APPID']);
define('APPDIR', $application_config['APPDIR']);
define('SHRDIR', $application_config['SHRDIR']);
define('IXTDIR', $application_config['IXTDIR']);
define('BASEURL', $application_config['BASEURL']);
define('ASSETS', 
	($application_config['ASSETS_PREPEND_BASEURL'] ? BASEURL : '') . 
	$application_config['ASSETS']
);


/**
 * Find and require the autoloader. 
 */
if (file_exists(APPDIR . 'autoload.php')) require APPDIR . 'autoload.php';
elseif (file_exists(SHRDIR . 'autoload.php')) require SHRDIR . 'autoload.php';
elseif (file_exists(IXTDIR . 'autoload.php')) require IXTDIR . 'autoload.php';
else throw new Exception('Autoloader not found.');

\Services\System\Profiler::start();
session_start();

/**
 * Load all of the config files. 
 */
$dirs = array(IXTDIR, SHRDIR, APPDIR);
foreach ($dirs as $dir) foreach (glob($dir . 'config/*.php') as $config_file) require $config_file;

/**
 * Run the application by starting the route which will call the appropriate controller.
 */
\Services\System\Profiler::event(\Services\System\Event_Type::NORMAL, 'IgniteXT', 'Start Application', 'Application has started running.');
\Services\System\Router::route(isset($_GET['ixt_route']) ? $_GET['ixt_route'] : '');
\Services\System\Profiler::event(\Services\System\Event_Type::NORMAL, 'IgniteXT', 'Finish Application', 'Application has finished running.');