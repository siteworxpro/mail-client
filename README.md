## Mail client for the siteworx pro email API

https://email.siteworxpro.com/api

Access to the API is restricted and requires an account.

`composer require siteworx/mail-client`

**Requires** PHP >7.0

Usage

```php
require 'vendor/autoload.php';

$transport = new Siteworx\Mail\Transports\ApiTransport([
	'client_id' => 'k4ndk...4kkfa',
	'client_secret' => 'Jdv4...4kvD'
]);

$client = new Siteworx\Mail\Client($transport);

$client->setSubject('Test Subject');
$client->setFrom('from@email.com');

$client->addTo('an@email.com');
$client->addTo('another@email.com');

$client->setBody('Test Message!');

$result = $client->send();
```

You can provide a cache to the api transport and your api token will 
automatically be cached for it's lifetime.

```php

$memcache = new Memcache;
$memcache->addServer($host);

$transport = new Siteworx\Mail\Transports\ApiTransport([
	'client_id' => 'k4ndk...4kkfa',
	'client_secret' => 'Jdv4...4kvD'
]);

$transport->setCache($memcache);
```

You can use any cache that implements the PSR-6 CacheInterface.

**Catching Message**

You can catch message if you are testing by passing in the value of `true` to the send method
```php
$client->send(true);
```
The payload will be sent to the api and validated but will be caught before it is sent.

**Delaying Messages**

You can delay message so they are sent at a specific time.

```php
$time = new DateTime();
$time->add(new DateInterval('P1D'));
$client->sendTime($time);
```
This will send the email the next day.