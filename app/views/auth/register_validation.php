<?php
// class for handling input validation for the registration form

class InputValidation {

    private static function isNotEmpty($input) {
        
        return !empty(trim($input));
    }

    private static function isAlphabetOnly($input) {
        if (preg_match('/\d/', $input)) {
            return false;
        }
        return true;
    }

    public static function validateName($name) {
        
        if (!self::isNotEmpty($name)){
            return '*Required';
        }
        
        if (!self::isAlphabetOnly($name)){
            return '*Must be alphabetic characters';
        }

        if (strlen($name) < 2) {
            return '*Must be at least two characters';
        }

        if (strlen($name) > 50) {
            return '*Must be less than 50 Characters';
        }
        return '';
    }

    public static function validateAddress($address) {

        if (!self::isNotEmpty($address)){
            return '*Required';
        }
        
        if (strlen($address) < 5) {
            return '*Must be at least 5 characters';
        }
        
        if (strlen($address) > 100) {
            return '*Must be less than 100 Characters';
        }
        return '';
    }

    public static function validateCity($city) {
        
        if (!self::isNotEmpty($city)) {
            return '*Required';
        }

        if (!preg_match("/^[a-zA-Z\s\-']+$/", $city)) {
            return '*Contains invalid characters';
        }

        if (strlen($city) < 5) {
            return '*Must be at least five characters';
        }

        if (strlen($city) > 50) {
            return '*Must be less than fifty characters';
        }

        return '';
    }

    public static function validateState($state) {
        
        if (!self::isNotEmpty($state)) {
            return '*Required';
        }
        
        if (!self::isAlphabetOnly($state)){
            return '*Must be alphabetic characters';
        }

        if (strlen($state) < 2) {
            return '*Must be at least two characters';
        }

        if (!preg_match("/^[A-Z]{2}$/", strtoupper($state))) {
            return '*State must be a valid 2-letter code (e.g., TX)';
        }
        return '';
    }

    public static function validateZipCode($zipCode) {
        if (!self::isNotEmpty($zipCode)) {
            return '*Required';
        }

        if (strlen($zipCode) < 4) {
            return '*Zipcode must be at least 4 numbers long';
        }

        if (!preg_match("/^\d{5}(-\d{4})?$/", $zipCode)) {
            return '*Invalid Zip Code format';
        }
        return '';
    }

    public static function validateEmail($email) {
        if (!self::isNotEmpty($email)) {
            return '*Email is required';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '*Invalid email format';
        }
        return '';
    }

    public static function validatePhoneNumber($phoneNumber) {
        if (!self::isNotEmpty($phoneNumber)) {
            return '*Phone Number is required';
        }

        if (!preg_match("/^\d{10}$/", preg_replace('/\D/', '', $phoneNumber))) {
            return '*Phone Number must contain 10 digits';
        }
        return '';
    }

    public static function validatePassword($password) {
        if (!self::isNotEmpty($password)) {
            return '*Required';
        }

        if (strlen($password) < 8) {
            return '*Must be at least 8 characters';
        }

        if (!preg_match("/[A-Z]/", $password)) {
            return '*Must contain at least one uppercase letter, one lowercase letter, and one number.';
        }

        if (!preg_match("/[a-z]/", $password)) {
            return '*Must contain at least one uppercase letter, one lowercase letter, and one number.';
        }

        if (!preg_match("/[0-9]/", $password)) {
            return '*Must contain at least one uppercase letter, one lowercase letter, and one number.';
        }
        return '';
    }

    public static function validatePasswordConfirmation($password, $passwordConfirmation) {
        if (!self::isNotEmpty($passwordConfirmation)) {
            return '*Required';
        }

        if ($password !== $passwordConfirmation) {
            return '*Passwords do not match';
        }
        return '';
    }
}