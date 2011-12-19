<?php
/**
 * IXT Form Validation Library
 * 
 * Validates user input. Can also set form values when validation fails and display
 * error messages either in a block or individually. 
 * 
 */
namespace Libraries;
class IXT_Form_Validation
{
	private $array = array();
	private $rules = array();
	private $errors = array();
	private $checked = false;
	private $valid = false;
	private $rule_class;
	
	private $pre_delim;
	private $post_delim;
	
	function valid() { return $this->valid; }
	function checked() { return $this->checked; }
	function checked_valid() { return ($this->checked && $this->valid); }
	function checked_invalid() { return ($this->checked && !$this->valid); }

	function clear_rules() { $this->rules = array(); }
	function get_rules() { return $this->rules; }

	function set_error($key, $message) { $this->errors[$key] = $message; $this->valid = false; }
	function clear_errors() { $this->errors = array(); }

	function set_delim($pre_delim, $post_delim) { $this->pre_delim = $pre_delim; $this->post_delim = $post_delim; }

	public function __construct($rule_class = null)
	{
		if ($rule_class == null) $this->rule_class = new \Libraries\IXT_Validation;
		else $this->rule_class = new $rule_class;
	}
	
	/**
	 * Takes the rules array and puts it into the format that I want.
	 * 
	 * In: array('name', 'label', 'rule,list')
	 * Out: 'name' => array( 'label' => 'label', 'rules' => 'rule,list' )
	 * 
	 * @param array $rules
	 */
	function set_rules($rules)
	{
		$this->rules = array();
		$this->add_rules($rules);
	}
	
	/**
	 * Same as set_rules but doesn't clear the rule array
	 * 
	 * @param type $rules 
	 */
	function add_rules($rules)
	{
		foreach ($rules as $rule)	
			$this->rules[ $rule[0] ] = array('label' => $rule[1], 'rules' => $rule[2]);
	}
	
	/**
	 * Uses the rules that were previously set to run validation on a given array.
	 * 
	 * @param array $array The array to validate. This will usually be $_GET or $_POST
	 * @return boolean $valid
	 */
	function validate($array)
	{
		$this->array = $array;
		$this->valid = true;
		foreach ($this->rules as $key => $info)
		{
			if (is_array($info) === false) throw new Exception('Invalid rule array.  Please make sure array is in this format: array( array("fieldname", "Field Label", "comma,separated,rule,list"),... )');
			$label = $info['label'];
			$rule_list = $info['rules'];
			$value = $this->get_value($key);
			
			if ($rule_list=='') continue;
			$rule_array = explode('|', $rule_list);
			
			foreach ($rule_array as $rule) 
			{
				if ($rule != 'required' && $value == '') continue;
				list($rule, $parameters) = $this->rule_parts($rule);
				$rule_class = $this->rule_class;
				array_unshift($parameters,$value);
				array_push($parameters,true);
				$output = call_user_func_array(array($rule_class,$rule),$parameters);
				if ($output !== true) $this->errors[$key] = 'The ' . $label . ' field' . $output;
			}
		}
		if (count($this->errors) > 0) $this->valid = false;
		$this->checked = true;
		return $this->valid;
	}
	
	function rule_parts($rule)
	{
		if (strpos($rule,'['))
		{
			if (substr($rule, -1) != ']') throw new Exception('Invalid rule parameter format.  Please make sure rule is in this format: rule[1,2]');
			$rule_parts = explode('[', $rule);
			list($rule,$parameters) = $rule_parts;
			$parameters = substr($parameters, 0, -1);
			$parameters = explode(',',$parameters);
		}
		else
		{
			$parameters = array();
		}
		return array($rule, $parameters);
	}

	/**
	 * This function returns the value of an array using a string like so:
	 * 'person' -> $array['person']
	 * 'people[5]' -> $array['people'][5]
	 * 
	 * @param array $array
	 * @param string $key
	 * @return string $value
	 */
	function get_value($key)
	{
		if (strpos($key, '[') !== false)
		{
			if (substr($key, -1) != ']') return false;
			$key_parts = explode('[', $key);
			$array1 = $key_parts[0];
			$array2 = $key_parts[1];
			$array2 = substr($array2, 0, -1);
			if (isset($this->array[$array1][$array2])) return $this->array[$array1][$array2];
		}
		else if (isset($this->array[$key])) return $this->array[$key];
		else return '';
	}
	
	/**
	 * Gets the error message for a single field.  The message is wrapped in delimiters.
	 * 
	 * @param string $key
	 * @return string $error_message
	 */
	function get_error($key)
	{
		if (!isset($this->errors[$key])) return '';
		else return $this->pre_delim . $this->errors[$key] . $this->post_delim;
	}

	/**
	 * Gets all of the error messages in a string. Each individual error message is wrapped in delimeters.
	 * You can optionally specify a separator that will be inserted between error messages (e.g. <br />)
	 * 
	 * @param string $separator
	 * @return string $error_message
	 */
	function get_errors($separator = '')
	{		
		$error_string = implode($this->post_delim . $separator . $this->pre_delim, $this->errors);
		return $this->pre_delim . $error_string . $this->post_delim;
	}

	/**
	 * Returns the form input value that was submitted for the specified key.
	 * 
	 * @param string $key
	 * @return string $value
	 */
	function form_value($key, $default='')
	{
		if ($this->checked == false) return $default;
		return htmlentities($this->getValue($key), ENT_QUOTES);
	}

	/**
	 * Returns the text need to select a dropdown item if the specified key/value pair is selected.
	 * 
	 * @param string $key
	 * @param string $value
	 * @return string $select_text
	 */
	function form_select($key, $value, $default = false)
	{
		if ($this->checked == false && $default == true) return 'selected="selected"';
		$field = $this->get_value($key);
		if ($field == $value) return 'selected="selected"';
		else return '';
	}
	
	/**
	 * Returns the text need to select a checkbox or radio if the specified key/value pair is selected.
	 * 
	 * @param string $key
	 * @param string $value
	 * @return string $check_text
	 */
	function form_check($key, $value, $default = false)
	{
		if ($this->checked == false && $default == true) return 'checked="checked"';
		$field = $this->get_value($key);
		if ($field == $value) return 'checked="checked"';
		else return '';
	}
}