<?php

namespace Shakurov\Coinbase\Exceptions;

use Exception;
use Illuminate\Http\Request;

class WebhookFailed extends Exception
{
    public static function missingSignature()
    {
        return new static('The request did not contain a header named `X-CC-Webhook-Signature`.');
    }

    public static function invalidSignature($signature)
    {
        return new static("The signature `{$signature}` found in the header named `X-CC-Webhook-Signature` is invalid. Make sure that the `coinbase.share_secret` config key is set to the value you found on the OhDear dashboard. If you are caching your config try running `php artisan clear:cache` to resolve the problem.");
    }

    public static function sharedSecretNotSet()
    {
        return new static('The OhDear webhook signing secret is not set. Make sure that the `ohdear-webhooks.signing_secret` config key is set to the value you found on the Stripe dashboard.');
    }

    public static function missingType()
    {
        return new static('The webhook call did not contain a type. Valid OhDear webhook calls should always contain a type.');
    }

    public function render($request)
    {
        return response(['error' => $this->getMessage()], 400);
    }
}