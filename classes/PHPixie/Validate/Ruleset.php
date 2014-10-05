<?php
namespace PHPixie\Validate;

/**
 * A set of rules for the validator
 * 
 * @package    Validate
 */
class Ruleset {

	/**
	 * Validates a Validator field.
	 * If the field is empty and the rule is other than 'filled'
	 * it will always be considered valid.
	 *
	 * @param   string $rule Rule to check against
	 * @param   array $params Rule parameters
	 * @param   string $filed Field to validate
	 * @param   \PHPixie\Validate\Validator Validator the field belongs to
	 * @return  bool If the field is valid
	 */
	public function validate($rule, $params, $field, $validator) {
		$value = $validator->get($field);
		$params = array_merge(
					array($value),
					$params,
					array($validator, $field)
				);
				
		if (empty($value))
			return $rule != 'filled';
			
		return call_user_func_array(array($this, 'rule_'.$rule), $params);
	}
	
	/**
	 * Checks if the value is not empty
	 *
	 * @param   mixed  $val  Value to check
	 * @return  bool 
	 */
	public function rule_filled($val) {
		return !empty($val);
	}
	
	/**
	 * Checks if the value consists of letters only
	 *
	 * @param   string  $val  Value to check
	 * @return  bool 
	 */
	public function rule_alpha($val) {
		return (bool) preg_match('/^\pL++$/uD', $val);
	}

	/**
	 * Checks if the value consists of numbers only
	 *
	 * @param   string  $val  Value to check
	 * @return  bool 
	 */
	public function rule_numeric($val) {
		return (bool)preg_match('#^[0-9]*$#',$val);
	}
	
	/**
	 * Checks if the value consists of letters and numbers only
	 *
	 * @param   string  $val  Value to check
	 * @return  bool 
	 */
	public function rule_alpha_numeric($val) {
		return (bool) preg_match('/^[\pL\pN]++$/uD', $val);
	}
	
	/**
	 * Checks if the value is a valid url slug. 
	 *
	 * @param   string  $val  Value to check
	 * @return  bool 
	 */
	public function rule_slug($val) {
		return (bool) preg_match('/^[\pL\pN\-]++$/uD', $val);
	}
	
	/**
	 * Checks if the value is a decimal number
	 *
	 * @param   string  $val  Value to check
	 * @return  bool 
	 */
	public function rule_decimal($val) {
		return (bool) preg_match('/^[0-9]+(?:\.[0-9]+)?$/D', $val);
	}
	
	
	/**
	 * Checks if the value is a phone number
	 *
	 * @param   string  $val  Value to check
	 * @return  bool 
	 */
	public function rule_phone($val) {
		return (bool)preg_match('#^[0-9\(\)\+ \-]*$#',$val);
	}
	
	/**
	 * Checks if the value is larger or equal to $min
	 * and smaller or equal to $max.
	 *
	 * @param   string  $val  Value to check
	 * @param   string  $min  Minimum allowed value
	 * @param   string  $min  Maximum allowed value
	 * @return  bool 
	 */
	public function rule_between($val, $min, $max) {
		return ($val>=$min && $val<=$max);
	}
	
	/**
	 * Checks if the value matches a regular expression
	 *
	 * @param   string  $val  Value to check
	 * @param   string  $regexp  Regular expression to test with
	 * @return  bool 
	 */
	public function rule_matches($val,$regexp){
		return (bool)preg_match($regexp,$val);
	}
	
	/**
	 * Checks if the value is an URL
	 *
	 * @param   string  $val  Value to check
	 * @return  bool 
	 */
	public function rule_url($val){
		return (bool)preg_match('~^
			[-a-z0-9+.]++://
			(?!-)[-a-z0-9]{1,63}+(?<!-)
			(?:\.(?!-)[-a-z0-9]{1,63}+(?<!-)){0,126}+
			(?::\d{1,5}+)?
			(?:/.*)?
			$~iDx', $val);
	}
	
	/**
	 * Checks if the value is an email
	 *
	 * @param   string  $val  Value to check
	 * @return  bool 
	 */
	public function rule_email($val) {
		return (bool) preg_match('/^[-_a-z0-9\'+*$^&%=~!?{}]++
			(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+
			@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})
			(?::\d++)?$/iDx', $val);
	}
	
	/**
	 * Checks if the character length is more or equal to $min
	 *
	 * @param   string  $val  Value to check
	 * @param   string  $min  Minimum characters
	 * @return  bool 
	 */
	public function rule_min_length($val, $min) {
		return strlen(utf8_decode($val))>=$min;
	}
	
	/**
	 * Checks if the character length is less or equal to $max
	 *
	 * @param   string  $val  Value to check
	 * @param   string  $min  Maximum characters
	 * @return  bool 
	 */
	public function rule_max_length($val, $max) {
		return strlen(utf8_decode($val))<=$max;
	}
	
	/**
	 * Checks if the character length is equal to $length
	 *
	 * @param   string  $val  Value to check
	 * @param   string  $length  Number of characters
	 * @return  bool 
	 */
	public function rule_length($val, $length) {
		return strlen(utf8_decode($val))<=$length;
	}
	
	/**
	 * Checks if the value is in the $allowed array
	 *
	 * @param   string  $val  Value to check
	 * @param   array  $allowed  Array of allowed values
	 * @return  bool 
	 */
	public function rule_in($val,$allowed) {
		return in_array($val,$allowed);
	}
	
	/**
	 * Checks if the value equals to $allowed
	 *
	 * @param   mixed  $val  Value to check
	 * @param   mixed  $allowed  Value to compare with
	 * @return  bool 
	 */
	public function rule_equals($val, $allowed) {
		return $val==$allowed;
	}
	
	/**
	 * Calls passed $callback function with the value as an argument.
	 * Good for validating using an inline function.
	 *
	 * @param   mixed     $val  Value to check
	 * @param   callable  $func  Function to call
	 * @return  bool 
	 */
	public function rule_callback($val,$callback, $field, $validator) {
		return call_user_func($callback, $val, $field, $validator);
	}
	
	/**
	 * Checks if the value is the same as the field with name
	 * $key is. E.g. to check if password confirmation field is same
	 * as password field.
	 *
	 * @param   mixed  $val  Value to check
	 * @param   mixed  $target_field  Field to comparw with
	 * @return  bool 
	 */
	public function rule_same_as($val,$target_field, $validator) {
		return $val == $validator->get($target_field);
	}
	
	
        /**
         * Allow add custom rule with lambda functions
         * @param string $val
         * @param function $fun
         * @return mixed
         */
        protected function rule_custom($val, $fun) {
            return $fun($val);
        }
	
}
