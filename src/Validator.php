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
    public static function validateEmailAddress(string $email): bool
    {
        return filter_var(self::extractEmailAddress($email), FILTER_VALIDATE_EMAIL) !== false;
    }


    private static function extractEmailAddress(string $email): string
    {
        $matches = [];
        preg_match('^<[a-zA-Z@.\-_]+>^', $email, $matches);

        return \count($matches) > 0 ? str_replace(['<', '>'], '', $matches[0]) : $email;
    }

}