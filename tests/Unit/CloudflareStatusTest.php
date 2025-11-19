<?php

use App\Enums\Cloudflare\SummaryStatus;
use App\Models\CloudflareStatus;

test('has expected array keys', function () {
    $status = new CloudflareStatus([
        'status' => SummaryStatus::None->value,
        'current_description' => 'All systems operational',
        'started_at' => now(),
        'ended_at' => null,
        'updated_at_cloudflare' => now(),
    ]);

    $array = $status->toArray();

    expect($array)->toHaveKeys([
        'status',
        'current_description',
        'started_at',
        'ended_at',
        'updated_at_cloudflare',
    ]);
});

test('has correct casts', function () {
    $status = new CloudflareStatus;

    $casts = $status->getCasts();

    expect($casts)
        ->toHaveKey('started_at', 'datetime')
        ->toHaveKey('ended_at', 'datetime')
        ->toHaveKey('updated_at_cloudflare', 'datetime')
        ->toHaveKey('status', SummaryStatus::class);
});
