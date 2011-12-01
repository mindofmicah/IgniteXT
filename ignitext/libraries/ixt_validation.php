<?php
/**
 * IXT Validation Library
 * 
 * Functions to validate data.
 */
namespace Libraries;
class IXT_Validation
{
	/**
	 * Input cannot be unset, null, or an empty string.
	 * 
	 * @param string $input
	 * @param boolean $return_error_message
	 * @return mixed $valid
	 */
	public function required($input, $return_error_message = false)
	{
		if (isset($input) && $input !== '') return true;
		else if ($return_error_message == true) return 'is required.';
		else return false;
	}
	
	/**
	 * Input must be a valid e-mail address.
	 * 
	 * @param string $input
	 * @param boolean $return_error_message
	 * @return mixed $valid
	 */
	public function email($input, $return_error_message = false)
	{
		//Regular Expression taken from Jonathan Gotti's EasyMail (http://jgotti.net)
		$valid = preg_match('/^(?:(?:(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|\x5c(?=[@,"\[\]' . 
			'\x5c\x00-\x20\x7f-\xff]))(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)' .
			'[@,"\[\]\x5c\x00-\x20\x7f-\xff]|\x5c(?=[@,"\[\]\x5c\x00-\x20\x7f-\xff])' .
			'|\.(?=[^\.])){1,62}(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@' .
			',"\[\]\x5c\x00-\x20\x7f-\xff])|[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]{1,2})|"' .
			'(?:[^"]|(?<=\x5c)"){1,62}")@(?:(?!.{64})(?:[a-zA-Z0-9][a-zA-Z0-9-]{1,61}' .
			'[a-zA-Z0-9]\.?|[a-zA-Z0-9]\.?)+\.(?:xn--[a-zA-Z0-9]+|[a-zA-Z]{2,6})|\[(?' .
			':[0-1]?\d?\d|2[0-4]\d|25[0-5])(?:\.(?:[0-1]?\d?\d|2[0-4]\d|25[0-5])){3}\])$/', $input);
		if ($valid) return true;
		else if ($return_error_message == true) return 'must be a valid e-mail address.';
		else return false;
	}
	
	/**
	 * Input must be an integer.  String must contain only numbers and leading zeros are not permitted.
	 * Integer input is also allowed.
	 * 
	 * @param string $input
	 * @param boolean $return_error_message
	 * @return mixed $valid
	 */
	public function integer($input, $return_error_message = false)
	{
		if ($input === (string)(int)$input || $input === (int)$input) return true;
		else if ($return_error_message == true) return 'must be an integer.';
		else return false;
	}
	
	/**
	 * Input must be numeric.  "+0123.45e6" and "0xFF" are considered numeric.
	 * 
	 * @param string $input
	 * @param boolean $return_error_message
	 * @return mixed $valid
	 */
	public function numeric($input, $return_error_message = false)
	{
		if (is_numeric($input)) return true;
		else if ($return_error_message == true) return 'must be a number.';
		else return false;
	}
	
	/**
	 * Input must be decimal or integer. 
	 * 
	 * @param string $input
	 * @param boolean $return_error_message
	 * @return mixed $valid
	 */
	public function decimal($input, $return_error_message = false)
	{
		if (preg_match("/^[-+]?[0-9]*\.?[0-9]+$/", $input)) return true;
		else if ($return_error_message == true) return 'must be a decimal number.';
		else return false;
	}
	
}

?>
