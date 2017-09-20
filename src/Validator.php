<?php

namespace Siteworx\Mail;

/**
 * Class Validator
 *
 * @package Siteworx
 */
class Validator
{
    /**
     * @param string $email
     * @return bool
     */
    public static function validateEmailAddress(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }
}