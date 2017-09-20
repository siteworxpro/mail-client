<?php

namespace Siteworx\Mail;

use Siteworx\Mail\Exceptions\ValidationException;
use Siteworx\Mail\Transports\TransportInterface;

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
    private $_subject = '(No Subject)';
    private $_body = '';
    private $_isHtml = false;

    private $_catch = false;

    /**
     * @var bool|\DateTimeInterface
     */
    private $_sendTime = false;

    public function __construct(TransportInterface $transport)
    {
        $this->_transport = $transport;
    }

    /**
     * @param string $to
     * @throws ValidationException
     */
    public function addTo(string $to)
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
    public function setBody(string $body, bool $isHtml = false)
    {
        $this->_body = $body;
        $this->_isHtml = $isHtml;
    }

    public function setSubject(string $subject)
    {
        $this->_subject = $subject;
    }

    public function setFrom(string $from)
    {
        $this->_from = $from;
    }

    public function send(bool $catch = false)
    {
        $this->_catch = $catch;
        $payload = $this->buildPayload();
        $result = $this->_transport->sentMailPayload($payload);

        return $result;
    }

    public function sendTime(\DateTimeInterface $sendTime)
    {
        $this->_sendTime = $sendTime;
    }

    private function buildPayload(): array
    {

        $mailPayload = [
            'Destination' => [
                'ToAddresses' => $this->_to
            ],
            'Message'     => [
                'Subject' => [
                    'Data' => $this->_subject
                ]
            ],
            'Source'      => $this->_from
        ];

        if ($this->_isHtml) {
            $mailPayload['Message']['Body']['Html']['Data'] = $this->_body;
            $mailPayload['Message']['Body']['Text']['Data'] = htmlentities($this->_body);
        } else {
            $mailPayload['Message']['Body']['Text']['Data'] = $this->_body;
        }

        if ($this->_catch) {
            $mailPayload['Catch'] = true;
        }

        if ($this->_sendTime !== false) {
            $mailPayload['ScheduledTime'] = $this->_sendTime->format('Y-m-d H:i:s');
        }

        return $mailPayload;

    }
}