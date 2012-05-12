<?php
namespace Controllers;

abstract class Index extends \System\Controller
{
	public static function index()
	{
		\System\Display::template('index');
	}
}
