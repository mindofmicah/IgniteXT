<?php
namespace System;
class Controller_Map
{
	private $dir;
	private $file;
	private $namespace;
	private $package;
	private $controller;
	private $action;
	private $leftovers;
	
	public function __set($property, $value) {
		switch($property)
		{
			default: $this->$property = $value; break;
		}
	}
	
	public function __get($property)
	{
		switch($property)
		{
			case 'fully_qualified_class': return $this->namespace . $this->controller; break;
			case 'fully_qualified_method': return $this->namespace . $this->controller . '::' . $this->action;
			default: return $this->$property; break;
		}
	}
}