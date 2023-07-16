<?php

namespace Shakurov\Coinbase\Tests;

use Shakurov\Coinbase\Facades\Coinbase;
use Shakurov\Coinbase\CoinbaseServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            CoinbaseServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            Coinbase::class,
        ];
    }

    protected function determineCoinbaseSignature(array $payload): string
    {
        $secret = config('coinbase.webhookSecret');

        $signature = hash_hmac('sha256', json_encode($payload), $secret);

        return $signature;
    }
}