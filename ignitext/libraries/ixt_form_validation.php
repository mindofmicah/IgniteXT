<?php
/**
 * IXT Form Library
 * 
 * Validates user input. Can also set form values when validation fails and display
 * error messages either in a block or individually.
 */
namespace Libraries;
class IXT_Form_Validation
{
	private $rules = array();
	private $errors = array();
	private $checked = false;
	private $valid = true;
	private $rule_functions = array();
	
	private $pre_delim;
	private $post_delim;
	
	public function is_valid() { return $this->valid; }
	public function is_checked() { return $this->checked; }

	public function set_rules($rules) { $this->rules = $rules; }
	public function add_rules($rules) { $this->rules = array_merge($this->rules, $rules); }
	public function clear_rules() { $this->rules = array(); }

	public function set_error($key, $message) { $this->errors[$key] = $message; $this->valid = false; }
	public function clear_errors() { $this->errors = array(); $this->valid = true; }

	public function set_delim($pre_delim,$post_delim) { $this->pre_delim = $pre_delim; $this->post_delim = $post_delim; }

	/**
	 * Uses the rules that were previously set to run validation on a given array.
	 * 
	 * @param array $form The array to validate. This will usually be $_GET or $_POST
	 * @return boolean $valid
	 */
	public function validate($form)
	{
		$this->valid = true;
		foreach ($this->rules as $key => $info)
		{
			list($name,$rule_list) = $info;
			$rule_array = explode(',',$rule_list);
			$value = $this->get_value($form, $key);
			foreach ($rule_array as $rule) 
			{
				$output = $this->$rule_functions[$rule]($value);
				if ($output !== true) $this->errors[$key] = str_replace('%name%',$name);
			}
		}
		if (count($this->errors)>0) $this->valid = false;
		$this->checked = true;
		return $this->valid;
	}

	
	public function get_error($key)
	{
		if ($this->checked==false) return '';
		if (!isset($this->errors[$key])) return '';
		else return $this->pre_delim . $this->errors[$key] . $this->post_delim;
	}

	public function get_all_errors($separator = '<br />')
	{
		if ($this->checked==false) return '';
		$error_string = implode($separator, $this->errors);
		return $this->pre_delim . $error_string . $this->post_delim;
	}

	public function get_all_errors_delim($separator = '')
	{
		if ($this->checked==false) return '';
		$error_string = implode($this->post_delim . $separator . $this->pre_delim, $this->errors);
		return $this->pre_delim . $error_string . $this->post_delim;
	}

	public function get_value($key)
	{
		if (!$this->_isset($key)) return '';
		else
		{
			$post =& $this->__post($key);
			return htmlentities($post,ENT_QUOTES);
		}
	}

	function get_select($key,$value)
	{
		if (!$this->__isset($key)) return '';
		else
		{
			$post =& $this->__post($key);
			if ($post==$value) return 'selected="selected"';
		}
	}

	function _required($key)
	{
		if (!$this->__isset($key)) return false;

		$post =& $this->__post($key);
		if ($post!='') return true;

		$this->errors[$key] = 'The ' . $this->rules[$key][0] . ' field is required.';
		return false;
	}

	function _email($key)
	{
		if (!$this->_required($key)) return false;
		if (!$this->__isset($key)) return false;

		$post =& $this->__post($key);
		if (preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/',$post)) return true;

		$this->errors[$key] = 'The ' . $this->rules[$key][0] . ' field must contain a valid e-mail address.';
		return false;
	}

	function _numeric($key)
	{
		if (!$this->__isset($key)) return false;
		$post =& $this->__post($key);
		if (is_numeric($post)) return true;
		return false;
	}

	function __isset($key)
	{
		if (strpos($key, '[') !== FALSE && preg_match('/(.*?)\[(.*?)\]/', $key, $matches))
		{
			if (isset($_POST[$matches[1]][$matches[2]])) return true;
		}
		else
		{
			if (isset($_POST[$key])) return true;
		}
		return false;
	}

	function &__post($key)
	{
		if (strpos($key, '[') !== FALSE && preg_match('/(.*?)\[(.*?)\]/', $key, $matches))
		{
			if (isset($_POST[$matches[1]][$matches[2]])) return $_POST[$matches[1]][$matches[2]];
		}
		else
		{
			if (isset($_POST[$key])) return $_POST[$key];
		}
	}
}