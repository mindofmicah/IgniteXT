<?php

function my_autoload($class)
{
  $valid_folders = array('models','libraries');
  
  $parts = explode('\\',$class);
  if (!in_array(strtolower($parts[0]),$valid_folders)) return;
  
  $filename = array_pop($parts);
  
  $path = implode('/',$parts);
  $path = str_replace('..','.',$path);
  $path = strtolower($path);
  
  include(APPDIR . $path . '/' . $filename . '.php');
}

spl_autoload_register('my_autoload');
