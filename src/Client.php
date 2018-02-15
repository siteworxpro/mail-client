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
    /**
     * @var TransportInterface
     */
    private $_transport;

    /**
     * @var array
     */
    private $_to = [];

    /**
     * @var array
     */
    private $_cc = [];

    /**
     * @var array
     */
    private $_bcc = [];

    /**
     * @var array
     */
    private $_files = [];

    /**
     * @var string
     */
    private $_from = '';

    /**
     * @var string
     */
    private $_subject = '(No Subject)';

    /**
     * @var string
     */
    private $_body = '';

    /**
     * @var bool
     */
    private $_isHtml = false;

    /**
     * @var bool
     */
    private $_catch = false;

    /**
     * @var bool|\DateTimeInterface
     */
    private $_sendTime = false;

    /**
     * Client constructor.
     *
     * @param TransportInterface $transport
     */
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
     * @param array $to
     * @throws ValidationException
     */
    public function setAllTo(array $to)
    {
        foreach ($to as $item) {
            if (!Validator::validateEmailAddress($item)) {
                throw new ValidationException('Email address is invalid');
            }
        }

        $this->_to = $to;
    }

    /**
     * @param string $cc
     * @throws ValidationException
     */
    public function addCc(string $cc)
    {
        if (!Validator::validateEmailAddress($cc)) {
            throw new ValidationException('Email address is invalid');
        }

        $this->_cc[] = $cc;
    }

    /**
     * @param string $bcc
     * @throws ValidationException
     */
    public function addBcc(string $bcc)
    {
        if (!Validator::validateEmailAddress($bcc)) {
            throw new ValidationException('Email address is invalid');
        }

        $this->_bcc[] = $bcc;
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

    /**
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->_subject = $subject;
    }

    /**
     * @param string $from
     *
     * @throws ValidationException
     */
    public function setFrom(string $from)
    {
        if (!Validator::validateEmailAddress($from)) {
            throw new ValidationException('Email address is invalid');
        }

        $this->_from = $from;
    }

    /**
     * @param bool $catch
     * @throws ValidationException
     * @return mixed
     */
    public function send(bool $catch = false)
    {
        $this->_catch = $catch;
        $payload = $this->_buildPayload();
        $result = $this->_transport->sentMailPayload($payload);

        return $result;
    }

    /**
     * @throws ValidationException
     * @return array
     */
    private function _buildPayload(): array
    {

        $this->_validateFields();

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

        if (!empty($this->_cc)) {
            $mailPayload['Destination']['CcAddresses'] = $this->_cc;
        }

        if (!empty($this->_bcc)) {
            $mailPayload['Destination']['BccAddresses'] = $this->_bcc;
        }

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

    /**
     * @throws ValidationException
     */
    private function _validateFields()
    {
        if (empty($this->_to)) {
            throw new ValidationException('To Address is required');
        }

        if (empty($this->_from)) {
            throw new ValidationException('From Address is required');
        }
    }

    /**
     * @param string $fileLocation
     *
     * @throws ValidationException
     */
    public function addAttachment(string $fileLocation)
    {
        if (!file_exists($fileLocation)) {
            throw new ValidationException('File does not exist.');
        }
        $file = fopen($fileLocation, 'r');
        $this->_files[] = $file;
    }

    /**
     * @param \DateTimeInterface $sendTime
     */
    public function sendTime(\DateTimeInterface $sendTime)
    {
        $this->_sendTime = $sendTime;
    }
}