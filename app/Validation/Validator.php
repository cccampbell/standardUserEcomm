<?php

namespace App\Validation;

use App\Model\Entry;
use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use App\Validation\Exceptions\UnknownValidationRule;
use App\Validation\Exceptions\UnknownValidationMethod;
use App\Validation\Exceptions\EmailUnavailableException;

class Validator {

    protected $errors;
    protected $types;
    private $entry;

    // ['one', 'two', 'three']
    // foreach

    public function __construct() {

        $this->types = include('types.php');
        $this->errors = [];
        $this->entry = new Entry();

    }   


    public function validate($inputs, $rules) {

        // takes in array of rules
        //                 //type   client input
        foreach($inputs as $key => $value) {

            // check each input against input rules
            try {
                $this->rules($key, $value, (array) $rules[$key]);
            } catch (Exception $e) {
                $e->getMessage();
                return;
            }
            

        }
        $_SESSION['validate_errors'] = $this->errors; 


        // return this instance of class 
        return $this;

    }

    // with param rules get selected rules
    private function rules($type, $input, array $rules) {
        
        $arr = [];

        // foreach rule
        foreach($rules as $rule) {

            if(array_key_exists($rule, $this->types)) {

                // get the key . 'Validate'
                $method = $rule . 'Validate';
                
                // var_dump(is_callable( array($this, $method) ));
                $find_method = array($this, $method);

                if(is_callable( $find_method, FALSE, $callable_method )) {

                    $check = (int) call_user_func($callable_method, $input.trim());


                    if(!$check) {

                        array_push($arr, $this->types[$rule]);

                    }
                    

                } else {

                    throw new UnknownValidationMethod();

                }

            } else {

                throw new UnknownValidationRule();

            }  


        }

        if(!empty($arr)) {
            $this->errors[$type] = $arr;
        }

    }

    public function failed() {

        // check if $errors is not empty
        return !empty($this->errors);
    }

    // param - $type (input type), $input (input value), $rules (array of rules)
    private function checkValidityOfInput($type, $input, $rules) {
        // temp array to store errors of type if any
        $arr = [];

        foreach ($rules as $key => $value) {

            if(!preg_match($key, $input)) {

                array_push($arr, $value);

            } else {

            }
        }

        if(!empty($arr)) {
            $this->errors[$type] = $arr;
        }

    }

    private function getInputNameRules() {

        return [
            '/^[a-z]*$/i' => 'Letters only, no digits or special characters',
            '/[a-z]{2,}$/i' => 'Minimum character length is 2',
        ];

    }

    private function postCodeFormatValidate($input) {

        return preg_match('/^[a-z]{1,2}[0-9]{1,2}[ ][0-9]{1,2}[a-z]{2}/i', $input);

    }

    private function notEmptyValidate($input) {
        
        // if input is empty
        if(preg_match('/^$/', $input)) {
            return false;
        } else {
            return true;
        }

    }

    private function containUppercaseValidate($input) {

        return preg_match('/(?=.*[A-Z])/', $input);

    }
    private function containLowercaseValidate($input) {

        return preg_match('/(?=.*[a-z])/', $input);

    }
    private function containNumberValidate($input) {
        return preg_match('/(?=.*[0-9])/', $input);
    }
    private function containSpecialValidate($input) {
        return preg_match('/(?=.*[!@#\$%\-_\^&\*])/', $input);
    }
    private function lengthMinMediumValidate($input) {
        return preg_match('/(?=.{8,})/', $input);
    }
    private function verifyPasswordValidate($input) {
        return preg_match('', $input);
    }
    private function emailAvailableValidate($input) {
        return $this->entry->isEmailAvailable($input);
    }
    private function lengthMinShortValidate($input) {
        return preg_match('/[a-z]{2,}$/i', $input);
    }
    private function nameValidate($input) {
        return preg_match('/^[a-z]*$/i', $input);
    }
    private function emailFormatValidate($input) {
        return preg_match('/^([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)@([a-zA-Z\-0-9]+\.).([a-zA-Z]{2,})$/', $input);
    }
    private function getInputEmailRules($input) {

        return [
            '/^([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)/' => 'Need before @ handle',
            '/@/' => 'need @',
            '/@([a-zA-Z\-0-9]+\.)/' => 'Need mail type',
            '/.([a-zA-Z]{2,})$/' => 'Need .com ? .co.uk ?',
        ];

    }
    private function getInputPasswordRules() {

        return [
            '/(?=.*[a-z])/' => 'Must contain at least one lower case letter',
            '/(?=.*[A-Z])/' => 'Must contain at least one uppercase letter',
            '/(?=.*[0-9])/' => 'Must contain at least one number',
            '/(?=.*[!@#\$%\-_\^&\*])/' => 'Must contain at least one special character "!@#$%^&*-_"',
            '/(?=.{8,})/' => 'Must be longer than 7 characters',
        ];

    }
    public function checkPasswords($input1, $input2) {

        if($input1 !== $input2) {

            if(!isset($this->errors['verify_password'])) {
                $arr = [];
                array_push($arr, 'Passwords do not match');
                $this->errors['verify_password'] = $arr;
            } else {
                array_push($this->errors['verify_password'], 'Passwords do not match');
            }

            $_SESSION['validate_errors'] = $this->errors;
            
        }

    }
}