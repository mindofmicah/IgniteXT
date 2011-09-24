<?
ini_set('display_errors',1); 
error_reporting(E_ERROR);

define('BASEURL','/');
define('BASEDIR',dirname(__FILE__).'/');
define('APPDIR',dirname(__FILE__).'/application/');
define('SYSDIR',dirname(__FILE__).'/system/');

require(SYSDIR . 'router.php');
require(SYSDIR . 'controller.php');
require(SYSDIR . 'model.php');
require(SYSDIR . 'database.php');
require(SYSDIR . 'autoload.php');

session_cache_limiter('public');
session_start();

foreach (glob(APPDIR . 'system/*.php') as $filename) include $filename;
foreach (glob(APPDIR . 'config/*.php') as $filename) include $filename;

$router = new Router;
$router->route();
