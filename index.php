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
define('SYSDIR',dirname(__FILE__).'/system/');

//Sets some PHP configuration options at runtime. Comment this line out if you 
//only want to use the settings from your server's php.ini file.
include BASEDIR . 'php_settings.php';

/**
 * Load System Files
 */
require SYSDIR . 'router.php';
require SYSDIR . 'controller.php';
require SYSDIR . 'model.php';
require SYSDIR . 'database.php';
require SYSDIR . 'autoload.php';
require SYSDIR . 'display.php';

session_cache_limiter('public');
session_start();

foreach (glob(APPDIR . 'system/*.php') as $filename) include $filename;
foreach (glob(APPDIR . 'config/*.php') as $filename) include $filename;

Router::route();