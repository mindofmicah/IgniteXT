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
 * Define Paths
 */
define('BASEURL','/');
define('BASEDIR',dirname(__FILE__).'/');
define('APPDIR',dirname(__FILE__).'/application/');
define('SHRDIR',dirname(__FILE__).'/shared/');
define('IXTDIR',dirname(__FILE__).'/ignitext/');

//Sets some PHP configuration options at runtime. Comment this line out if you 
//only want to use the settings from your server's php.ini file.
include BASEDIR . 'php_settings.php';

if (file_exists(APPDIR . 'system/autoload.php')) include APPDIR . 'system/autoload.php';
elseif (file_exists(SHRDIR . 'system/autoload.php')) include SHRDIR . 'system/autoload.php';
elseif (file_exists(IXTDIR . 'system/autoload.php')) include IXTDIR . 'system/autoload.php';
else throw new Exception('Autoloader not found.');

session_start();

$dirs = array(IXTDIR, SHRDIR, APPDIR);
foreach ($dirs as $dir) foreach (glob($dir . 'config/*.php') as $config_file) include $config_file;

\System\Router::route();