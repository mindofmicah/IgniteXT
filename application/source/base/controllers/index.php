<?php
namespace Controllers;

use \Services\System\Display;

abstract class Index extends \Services\System\Controller
{
	public static function index()
	{
		Display::template('index');
	}
}
