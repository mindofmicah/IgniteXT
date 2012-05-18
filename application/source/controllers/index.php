<?php
namespace Controllers;

abstract class Index extends \Services\System\Controller
{
	public static function index()
	{
		\System\Display::template('index');
	}
}
