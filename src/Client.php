<?php

namespace Siteworx\Mail;

use Siteworx\Mail\Exceptions\ValidationException;

/**
 * Class Client
 *
 * @package Siteworx
 */
class Client
{
    private $_transport;

    private $_to = [];
    private $_from = '';
    private $_subject = '';
    private $_body = '';
    private $_isHtml = false;

    public function __construct(TransportInterface $transport)
    {
        $this->_transport = $transport;
    }

    /**
     * @param string $to
     * @throws ValidationException
     */
    public function addTo(string $to): void
    {
        if (!Validator::validateEmailAddress($to)) {
            throw new ValidationException('Email address is invalid');
        }

        $this->_to[] = $to;

    }

    /**
     * @param string $body
     * @param bool   $isHtml
     */
    public function setBody(string $body, bool $isHtml = false): void
    {
        $this->_body = $body;
        $this->_isHtml = $isHtml;
    }

    public function setSubject(string $subject): void
    {
        $this->_subject = $subject;
    }

    public function setFrom(string $from): void
    {
        $this->_from = $from;
    }

    public function send(): array
    {
        $this->buildPayload();

    }

    private function buildPayload(): array
    {

        $return = [

        ];

        return $return;

    }
}