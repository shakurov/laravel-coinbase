# Laravel wrapper for the Coinbase Commerce API

## Installation

You can install the package via composer:

```bash
composer require shakurov/coinbase
```

The service provider will automatically register itself.

You must publish the config file with:
```bash
php artisan vendor:publish --provider="Shakurov\Coinbase\CoinbaseServiceProvider" --tag="config"
```

This is the contents of the config file that will be published at `config/coinbase.php`:

```php
return [
    'apiKey' => env('COINBASE_API_KEY'),
    'apiVersion' => env('COINBASE_API_VERSION'),
    
    'webhookSecret' => env('COINBASE_WEBHOOK_SECRET'),
    'webhookJobs' => [
        // 'charge:created' => \App\Jobs\CoinbaseWebhooks\HandleCreatedCharge::class,
        // 'charge:confirmed' => \App\Jobs\CoinbaseWebhooks\HandleConfirmedCharge::class,
        // 'charge:failed' => \App\Jobs\CoinbaseWebhooks\HandleFailedCharge::class,
        // 'charge:delayed' => \App\Jobs\CoinbaseWebhooks\HandleDelayedCharge::class,
        // 'charge:pending' => \App\Jobs\CoinbaseWebhooks\HandlePendingCharge::class,
        // 'charge:resolved' => \App\Jobs\CoinbaseWebhooks\HandleResolvedCharge::class,
    ],
    'webhookModel' => Shakurov\Coinbase\Models\CoinbaseWebhookCall::class,
];

```

In the `webhookSecret` key of the config file you should add a valid webhook secret. You can find the secret used at [the webhook configuration settings on the Coinbase Commerce dashboard](https://commerce.coinbase.com/dashboard/settings).

Next, you must publish the migration with:
```bash
php artisan vendor:publish --provider="Shakurov\Coinbase\CoinbaseServiceProvider" --tag="migrations"
```

After the migration has been published you can create the `coinbase_webhook_calls` table by running the migrations:

```bash
php artisan migrate
```

Finally, take care of the routing: At [the Coinbase Commerce dashboard](https://commerce.coinbase.com/dashboard/settings) you must add a webhook endpoint, for example: `https://example.com/api/coinbase/webhook`

## Usage

### Charges

List charges:
```php
$charges = Coinbase::getCharges();
```

Create a charge:
```php
$charge = Coinbase::createCharge([
    'name' => 'Name',
    'description' => 'Description',
    'local_price' => [
        'amount' => 100,
        'currency' => 'USD',
    ],
    'pricing_type' => 'fixed_price',
]);
```

Show a charge:
```php
$charge = Coinbase::getCharge($chargeId);
```

### Checkouts

List checkouts:
```php
$checkouts = Coinbase::getCheckouts();
```

Create a checkout:
```php
$checkout = Coinbase::createCheckout([
    'name' => 'Name',
    'description' => 'Description',
    'local_price' => [
        'amount' => 100,
        'currency' => 'USD',
    ],
    'pricing_type' => 'fixed_price',
]);
```

Show a checkout:
```php
$checkout = Coinbase::getCheckout($checkoutId);
```

Update a checkout:
```php
$checkout = Coinbase::updateCheckout($checkoutId, [
    'name' => 'New Name',
    'description' => 'New Description',
    'local_price' => [
        'amount' => 200,
        'currency' => 'USD',
    ],
    'requested_info' => [
        'name',
    ],
]);
```

### Events

List events:
```php
$events = Coinbase::getEvents();
```

Show an event:
```php
$event = Coinbase::getEvent($eventId);
```

### Webhooks

Coinbase Commerce will send out webhooks for several event types. You can find the [full list of events types](https://commerce.coinbase.com/docs/api/#webhooks) in the Coinbase Commerce documentation.

Coinbase Commerce will sign all requests hitting the webhook url of your app. This package will automatically verify if the signature is valid. If it is not, the request was probably not sent by Coinbase Commerce.
 
Unless something goes terribly wrong, this package will always respond with a `200` to webhook requests. Sending a `200` will prevent Coinbase Commerce from resending the same event over and over again. All webhook requests with a valid signature will be logged in the `coinbase_webhook_calls` table. The table has a `payload` column where the entire payload of the incoming webhook is saved.

If the signature is not valid, the request will not be logged in the `coinbase_webhook_calls` table but a `Shakurov\Coinbase\Exceptions\WebhookFailed` exception will be thrown.
If something goes wrong during the webhook request the thrown exception will be saved in the `exception` column. In that case the controller will send a `500` instead of `200`. 
 
There are two ways this package enables you to handle webhook requests: you can opt to queue a job or listen to the events the package will fire.
 
 
### Handling webhook requests using jobs 
If you want to do something when a specific event type comes in you can define a job that does the work. Here's an example of such a job:

```php
<?php

namespace App\Jobs\CoinbaseWebhooks;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Shakurov\Coinbase\Models\CoinbaseWebhookCall;

class HandleCreatedCharge implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    
    /** @var \Shakurov\Coinbase\Models\CoinbaseWebhookCall */
    public $webhookCall;

    public function __construct(CoinbaseWebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        // do your work here
        
        // you can access the payload of the webhook call with `$this->webhookCall->payload`
    }
}
```

We highly recommend that you make this job queueable, because this will minimize the response time of the webhook requests. This allows you to handle more Coinbase Commerce webhook requests and avoid timeouts.

After having created your job you must register it at the `jobs` array in the `coinbase.php` config file. The key should be the name of [the coinbase commerce event type](https://commerce.coinbase.com/docs/api/#webhooks) where but with the `.` replaced by `_`. The value should be the fully qualified classname.

```php
// config/coinbase.php

'jobs' => [
    'charge:created' => \App\Jobs\CoinbaseWebhooks\HandleCreatedCharge::class,
],
```

### Handling webhook requests using events

Instead of queueing jobs to perform some work when a webhook request comes in, you can opt to listen to the events this package will fire. Whenever a valid request hits your app, the package will fire a `coinbase::<name-of-the-event>` event.

The payload of the events will be the instance of `CoinbaseWebhookCall` that was created for the incoming request. 

Let's take a look at how you can listen for such an event. In the `EventServiceProvider` you can register listeners.

```php
/**
 * The event listener mappings for the application.
 *
 * @var array
 */
protected $listen = [
    'coinbase::charge:created' => [
        App\Listeners\ChargeCreatedListener::class,
    ],
];
```

Here's an example of such a listener:

```php
<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Shakurov\Coinbase\Models\CoinbaseWebhookCall;

class ChargeCreatedListener implements ShouldQueue
{
    public function handle(CoinbaseWebhookCall $webhookCall)
    {
        // do your work here

        // you can access the payload of the webhook call with `$webhookCall->payload`
    }   
}
```

We highly recommend that you make the event listener queueable, as this will minimize the response time of the webhook requests. This allows you to handle more Coinbase Commerce webhook requests and avoid timeouts.

The above example is only one way to handle events in Laravel. To learn the other options, read [the Laravel documentation on handling events](https://laravel.com/docs/5.6/events). 

## Advanced usage

### Retry handling a webhook

All incoming webhook requests are written to the database. This is incredibly valuable when something goes wrong while handling a webhook call. You can easily retry processing the webhook call, after you've investigated and fixed the cause of failure, like this:

```php
use Shakurov\Coinbase\Models\CoinbaseWebhookCall;

CoinbaseWebhookCall::find($id)->process();
```

### Performing custom logic

You can add some custom logic that should be executed before and/or after the scheduling of the queued job by using your own model. You can do this by specifying your own model in the `model` key of the `coinbase` config file. The class should extend `Shakurov\Coinbase\Models\CoinbaseWebhookCall`.

Here's an example:

```php
use Shakurov\Coinbase\Models\CoinbaseWebhookCall;

class MyCustomWebhookCall extends CoinbaseWebhookCall
{
    public function process()
    {
        // do some custom stuff beforehand
        
        parent::process();
        
        // do some custom stuff afterwards
    }
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.


## Backers

- [@antimech](https://github.com/antimech)
