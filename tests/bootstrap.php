<?php
require 'Patchwork.php';

define('BASEDIR', dirname(__FILE__) . '/../');
define('APPDIR', BASEDIR . 'application/');
define('SHRDIR', BASEDIR . 'shared/');
define('IXTDIR', BASEDIR . 'ignitext/');
require BASEDIR . 'ignitext/system/autoload.php';

define('APPID', 'unit_test');