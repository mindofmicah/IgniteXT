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
 * Configure Paths
 */
define('BASEURL','/');
define('BASEDIR',dirname(__FILE__).'/');
define('APPDIR',dirname(__FILE__).'/application/');
define('SHRDIR',dirname(__FILE__).'/shared/');
define('IXTDIR',dirname(__FILE__).'/ignitext/');

//Sets some PHP configuration options at runtime. Comment this line out if you 
//only want to use the settings from your server's php.ini file.
include BASEDIR . 'php_settings.php';

require IXTDIR . 'system/autoload.php';

session_cache_limiter('public');
session_start();

foreach (glob(IXTDIR . 'config/*.php') as $filename) include $filename;
foreach (glob(SHRDIR . 'config/*.php') as $filename) include $filename;
foreach (glob(APPDIR . 'config/*.php') as $filename) include $filename;

\System\Router::route();