<?php
/*==============================================================================*
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
 *==============================================================================*/

/**
 * Define locations of directories used by IgniteXT.
 */
define('BASEDIR', dirname(__FILE__) . '/');
define('APPDIR', dirname(__FILE__) . '/application/');
define('SHRDIR', dirname(__FILE__) . '/shared/');
define('IXTDIR', dirname(__FILE__) . '/ignitext/');

/**
 * Define the base URL. This relative URL should point to the location that
 * contains your index.php and assets folder.
 */
define('BASEURL', '/');

/**
 * Define the application identifier. This will be used by system classes to 
 * prevent multiple applications from interfering with each other when using 
 * shared resources such as PHP sessions.
 */
define('APPID', 'my_application');

/**
 * Sets some PHP configuration options at runtime. Comment this line out if you 
 * only want to use the settings from your server's php.ini file.
 */
include BASEDIR . 'php_settings.php';

/**
 * Find and require the autoloader. 
 */
if (file_exists(APPDIR . 'system/autoload.php')) require APPDIR . 'system/autoload.php';
elseif (file_exists(SHRDIR . 'system/autoload.php')) require SHRDIR . 'system/autoload.php';
elseif (file_exists(IXTDIR . 'system/autoload.php')) require IXTDIR . 'system/autoload.php';
else throw new Exception('Autoloader not found.');

session_start();

/**
 * Load all of the config files. 
 */
$dirs = array(IXTDIR, SHRDIR, APPDIR);
foreach ($dirs as $dir) foreach (glob($dir . 'config/*.php') as $config_file) include $config_file;

/**
 * Run the application by starting the route which will call the appropriate controller.
 */
\System\Event::event( \System\Event_Type::NORMAL, 'IgniteXT', 'Start Application', 'Application has started running.');
\System\Router::route();
\System\Event::event( \System\Event_Type::NORMAL, 'IgniteXT', 'Finish Application', 'Application has finished running.');
\System\Event::display();