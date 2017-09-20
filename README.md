# Mail client for the siteworx pro email API

https://email.siteworxpro.com/api

Access to the API is restricted and requires and account.

`composer require siteworx/mail-client`

**Requires** PHP >7.0

Usage

```
require 'vendor/autoload.php';

$transport = new Siteworx\Mail\Transports\MailTransport([
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
