<?php
/**
 * This configuration file sets PHP settings at runtime.
 */

//Display errors to screen
ini_set('display_errors', 1);

//Set which errors to display and/or log
error_reporting(E_ALL ^ E_NOTICE);

//Enable short PHP open tags
ini_set('short_open_tag', 1);