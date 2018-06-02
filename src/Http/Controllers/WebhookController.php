<?php

namespace Shakurov\Coinbase\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Shakurov\Coinbase\Http\Middleware\VerifySignature;

class WebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware(VerifySignature::class);
    }

    public function __invoke(Request $request)
    {
        $payload = $request->input();

        $model = config('coinbase.webhookModel');

        $coinbaseWebhookCall = $model::create([
            'type' =>  $payload['event']['type'] ?? '',
            'payload' => $payload,
        ]);

        try {
            $coinbaseWebhookCall->process();
        } catch (\Exception $e) {
            $coinbaseWebhookCall->saveException($e);

            throw $e;
        }
    }
}