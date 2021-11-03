<?php

/**
 * 
 * bootstrap_validateInput: Return class 'is-valid' or 'is-invalid' depends of $validation argument
 * 
 * Argument 1: $validation can be a closure or bool like $result
 * 
 * @return string is-valid | is-invalid
 * 
 */
if( ! function_exists('bootstrap_validateInput') )
{
    function bootstrap_validateInput($validation, $add_classes_valid = [], $add_classes_invalid = [])
    {
        $add_classes_valid   = arrayToString($add_classes_valid);
        $add_classes_invalid = arrayToString($add_classes_invalid);
        $result_validation   = (bool) is_callable($validation) ? $validation() : $validation;
        
        return $result_validation ? "is-valid {$add_classes_valid}" : "is-invalid {$add_classes_invalid}";
    }
}

/**
 * 
 * bootstrap_isInputInvalid: Return class 'is-invalid' depends if $name are in $errors
 * 
 * Argument 1: $name the name of the error
 * Argument 2: $errors is a instance ViewErrorBag from Laravel
 * Argument 3: $return_valid_class if is true, return 'is-valid', otherwise return null
 * 
 * @return string is-valid | is-invalid
 * 
 */
if( ! function_exists('bootstrap_isInputInvalid') )
{
    function bootstrap_isInputInvalid($name, \Illuminate\Support\ViewErrorBag $errors, $add_classes = [], $return_valid_class = false)
    {
        $add_classes = arrayToString($add_classes);

        if( $errors->has($name) )
            return "is-invalid {$add_classes}";

        return ! $return_valid_class ?: 'is-valid';
    }
}