<?php
/**
 * IXT Validation Library
 * 
 * Functions to validate data.
 * 
 * alphanumeric - must be an alphanumeric string.
 * decimal - must be decimal or integer.
 * email - must be a valid e-mail address.
 * email_list - must contain a comma separated list of valid e-mail addresses.
 * integer - must be an integer.  String must contain only numbers and leading zeros are not permitted.
 * numeric - must be numeric.  "+0123.45e6" and "0xFF" are considered numeric.
 * required - cannot be unset, null, or an empty string.
 * 
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
	function required($input, $return_error_message = false)
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
	function email($input, $return_error_message = false)
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
	 * Input must contain a comma separated list of valid e-mail addresses.
	 * 
	 * @param string $input
	 * @param boolean $return_error_message
	 * @return mixed $valid
	 */
	function email_list($input, $return_error_message = false)
	{
		$emails = explode(',', $input);
		
		$valid = true;
		foreach ($emails as $email)	
			if (self::email($email) !== true) { $valid = false; break; }
			
		if ($valid) return true;
		else if ($return_error_message == true) return 'must contain a comma separated list of valid e-mail addresses.';
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
	function integer($input, $return_error_message = false)
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
	function numeric($input, $return_error_message = false)
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
	function decimal($input, $return_error_message = false)
	{
		if (preg_match("/^[-+]?[0-9]*\.?[0-9]+$/", $input)) return true;
		else if ($return_error_message == true) return 'must be a decimal number.';
		else return false;
	}
	
	/**
	 * Input must be an alphanumeric string.
	 * 
	 * @param string $input
	 * @param boolean $return_error_message
	 * @return mixed $valid
	 */
	function alphanumeric($input, $return_error_message = false)
	{
		if (preg_match("/^[a-zA-Z0-9]*$/", $input)) return true;
		else if ($return_error_message == true) return 'must be an alphanumeric string.';
		else return false;
	}
	
	/**
	 * Input must contain only letters.
	 * 
	 * @param string $input
	 * @param boolean $return_error_message
	 * @return mixed $valid
	 */
	function alpha($input, $return_error_message = false)
	{
		if (preg_match("/^[a-zA-Z]*$/", $input)) return true;
		else if ($return_error_message == true) return 'must contain only letters.';
		else return false;
	}
	
	/**
	 * Input must be greater than the specified number
	 * 
	 * @param string $input
	 * @param integer $greater_than
	 * @param boolean $return_error_message
	 * @return mixed $valid
	 */
	function greater_than($input, $greater_than, $return_error_message = false)
	{
		$numeric = self::numeric($input);
		if ($numeric !== true) return $numeric;
		if ($input>$greater_than) return true;
		else if ($return_error_message == true)	return 'must be greater than ' . $greater_than . '.';
		else return false;
	}
	
	/**
	 * Input must be less than the specified number
	 * 
	 * @param string $input
	 * @param integer $less_than
	 * @param boolean $return_error_message
	 * @return mixed $valid
	 */
	function less_than($input, $less_than, $return_error_message = false)
	{
		$numeric = self::numeric($input);
		if ($numeric !== true) return $numeric;
		if ($input<$less_than) return true;
		else if ($return_error_message == true)	return 'must be less than ' . $less_than . '.';
		else return false;
	}
	
}

?>
