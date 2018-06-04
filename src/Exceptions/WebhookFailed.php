<?php

namespace Shakurov\Coinbase\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Shakurov\Coinbase\Models\CoinbaseWebhookCall;

class WebhookFailed extends Exception
{
    public static function missingSignature()
    {
        return new static('The request did not contain a header named `X-CC-Webhook-Signature`.');
    }

    public static function invalidSignature($signature)
    {
        return new static("The signature `{$signature}` found in the header named `X-CC-Webhook-Signature` is invalid. Make sure that the `coinbase.webhookSecret` config key is set to the value you found on the Coinbase Commerce dashboard. If you are caching your config try running `php artisan clear:cache` to resolve the problem.");
    }

    public static function sharedSecretNotSet()
    {
        return new static('The Coinbase Commerce webhook shared secret is not set. Make sure that the `coinbase.webhookSecret` config key is set to the value you found on the Coinbase Commerce dashboard.');
    }
    
    public static function jobClassDoesNotExist(string $jobClass, CoinbaseWebhookCall $webhookCall)
    {
        return new static("Could not process webhook id `{$webhookCall->id}` of type `{$webhookCall->type} because the configured jobclass `$jobClass` does not exist.");
    }

    public static function missingType()
    {
        return new static('The webhook call did not contain a type. Valid Coinbase Commerce webhook calls should always contain a type.');
    }

    public function render($request)
    {
        return response(['error' => $this->getMessage()], 400);
    }
}
