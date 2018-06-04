<?php

namespace Shakurov\Coinbase\Tests\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Shakurov\Coinbase\Tests\TestCase;
use Shakurov\Coinbase\Http\Middleware\VerifySignature;

class VerifySignatureTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Route::post('coinbase-webhook', function () {
            return 'ok';
        })->middleware(VerifySignature::class);
    }

    /** @test */
    public function it_will_succeed_when_the_request_has_a_valid_signature()
    {
        $payload = ['event' => ['type' => 'charge:created']];

        $response = $this->postJson(
            'coinbase-webhook',
            $payload,
            ['X-CC-Webhook-Signature' => $this->determineCoinbaseSignature($payload)]
        );

        $response
            ->assertStatus(200)
            ->assertSee('ok');
    }

    /** @test */
    public function it_will_fail_when_the_signature_header_is_not_set()
    {
        $response = $this->postJson(
            'coinbase-webhook',
            ['event' => ['type' => 'charge:created']]
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => 'The request did not contain a header named `X-CC-Webhook-Signature`.',
            ]);
    }

    /** @test */
    public function it_will_fail_when_the_signature_is_invalid()
    {
        $response = $this->postJson(
            'coinbase-webhook',
            ['event' => ['type' => 'charge:created']],
            ['X-CC-Webhook-Signature' => 'abc']
        );

        $response
            ->assertStatus(400)
            ->assertSee('found in the header named `X-CC-Webhook-Signature` is invalid');
    }
}