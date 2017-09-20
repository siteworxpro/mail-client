<?php

namespace Siteworx\Mail\Transports;

use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

interface TransportInterface
{

    public function setCache(CacheInterface $cache);

    public function setLogger(LoggerInterface $logger);

    public function sentMailPayload(array $payload);

}