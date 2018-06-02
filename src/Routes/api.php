<?php

Route::group(['prefix' => 'api',  'middleware' => 'api'], function() {
    Route::post('coinbase/webhook', '\Shakurov\Coinbase\Http\Controllers\WebhookController')->name('coinbase-webhook');
});