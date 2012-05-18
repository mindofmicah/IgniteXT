<?php
require 'Patchwork.php';

define('BASEDIR', dirname(__FILE__) . '/../');
define('TESTDIR', BASEDIR . 'tests/');
define('APPDIR', BASEDIR . 'application/');
define('SHRDIR', BASEDIR . 'shared/');
define('IXTDIR', BASEDIR . 'ignitext/');
define('APPID', 'unit_test');

require BASEDIR . 'ignitext/autoload.php';

require 'mock_autoloader.php';