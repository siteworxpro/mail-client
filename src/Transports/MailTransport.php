<?php

namespace Siteworx\Mail\Transports;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class MailTransport implements TransportInterface
{

    /**
     * @var string
     */
    private $_apiEndpoint = 'https://email.siteworxpro.com';

    /**
     * @var string
     */
    private $_clientId = '';

    /**
     * @var string
     */
    private $_clientSecret = '';

    /**
     * @var string
     */
    private $_accessToken = '';

    /**
     * @var Guzzle
     */
    private $_client;

    /**
     * @var CacheInterface
     */
    private $_cache = null;

    /**
     * @var LoggerInterface
     */
    private $_logger = null;

    /**
     * Client constructor.
     *
     * @param array $config
     * @throws \Exception
     */
    public function __construct(array $config = [])
    {
        if (!isset($config['client_id'])) {
            throw new \Exception('Client ID is missing.');
        }

        if (!isset($config['client_secret'])) {
            throw new \Exception('Client Secret missing.');
        }

        $this->_client = new Guzzle();
        $this->_clientId = $config['client_id'];
        $this->_clientSecret = $config['client_secret'];


    }

    /**
     * @param mixed $Cache
     */
    public function setCache(CacheInterface $Cache): void
    {
        $this->_cache = $Cache;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->_logger = $logger;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->_accessToken;
    }

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId): void
    {
        $this->_clientId = $clientId;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret): void
    {
        $this->_clientSecret = $clientSecret;
    }

    /**
     * @param array $payload
     * @return \stdClass
     */
    public function sentMailPayload(array $payload): \stdClass
    {

        $this->setToken();

        $this->_logger->info('Sending Email.');

        try {
            $result = $this->_client->post($this->_apiEndpoint . '/api/email', [
                'form_params' => $payload,
                'headers'     => [
                    'Authorization' => 'Bearer ' . $this->_accessToken
                ]
            ]);

            $body = $result->getBody()->getContents();
            $data = json_decode($body);

            $this->_logger->info('Success!');
            $this->_logger->debug(\json_encode($body));

        } catch (ServerException $exception) {

            $result = $exception->getResponse();
            $body = $result->getBody()->getContents();

            $data = json_decode($body);

            $this->_logger->warning('An error occurred sending the email! (' . $result->getStatusCode() . ')');
            $this->_logger->debug(\json_encode($body));

        } catch (RequestException $exception) {

            $result = $exception->getResponse();
            $body = $result->getBody()->getContents();

            $data = json_decode($body);

            $this->_logger->warning('An error occurred sending the email! (' . $result->getStatusCode() . ')');
            $this->_logger->debug(\json_encode($body));

        }

        return $data;

    }

    private function setToken(): void
    {
        if ($this->_cache !== null) {
            $this->_accessToken = $this->_cache->get('access_token');
        } else {
            if ($this->_logger !== null) {
                $this->_logger->notice('No cache available for client. Providing a cache interface can improve client response times.');
            }
        }

        if (empty($this->_accessToken)) {
            $this->refreshToken();
        }

    }

    /**
     * @return \stdClass
     */
    private function refreshToken(): \stdClass
    {
        $params = [
            'scope'         => 'default',
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->_clientId,
            'client_secret' => $this->_clientSecret
        ];
        try {
            $result = $this->_client->post($this->_apiEndpoint . '/access_token', [
                'form_params' => $params
            ]);

            $body = $result->getBody()->getContents();
            $data = json_decode($body);

            $this->_accessToken = $data->access_token;

            if ($this->_cache !== null) {
                $this->_cache->set('access_token', $this->_accessToken, $data->expires_in);
            }

        } catch (ServerException $exception) {

            $result = $exception->getResponse();
            $body = $result->getBody()->getContents();

            $data = json_decode($body);

        }

        return $data;

    }

}