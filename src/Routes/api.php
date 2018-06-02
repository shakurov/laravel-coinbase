<?php

Route::post('coinbase/webhook', '\Shakurov\Coinbase\Http\Controllers\WebhookController')->name('coinbase-webhook');