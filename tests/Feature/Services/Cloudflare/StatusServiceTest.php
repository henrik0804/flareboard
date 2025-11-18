<?php

use App\DataTransferObjects\CloudflareStatusResponse;
use App\Enums\Cloudflare\SummaryStatus;
use App\Services\Cloudflare\StatusService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->endpoint = 'https://www.cloudflarestatus.com/api/v2/summary.json';
    $this->service = app(StatusService::class);
    $this->page = [
        'id' => 'yh6f0r4529hc',
        'name' => 'Cloudflare',
        'url' => 'https://www.cloudflarestatus.com',
        'updated_at' => Date::now(),
    ];
});

describe('summary', function () {
    it('retrieves the summary from Cloudflare status API', function () {
        Http::fake([
            $this->endpoint => Http::response([
                'page' => $this->page,
                'status' => [
                    'indicator' => 'none',
                    'description' => 'All Systems Operational',
                ],
            ]),
        ]);

        $summary = $this->service->summary();

        expect($summary)
            ->toBeInstanceOf(CloudflareStatusResponse::class)
            ->and($summary->status)->toBe(SummaryStatus::None)
            ->and($summary->message)->toBe('All Systems Operational');
    });

    it('throws an exception when the API request fails', function () {
        Http::fake([
            $this->endpoint => Http::response([], 500),
        ]);

        $this->service->summary();
    })->throws(RequestException::class);

    it('throws an exception when the API is unreachable', function () {
        Http::fake([
            $this->endpoint => Http::response([], 404),
        ]);

        $this->service->summary();
    })->throws(RequestException::class);
});

describe('isOperational', function () {
    it('returns true when all systems are operational', function () {
        Http::fake([
            $this->endpoint => Http::response([
                'page' => $this->page,
                'status' => [
                    'indicator' => 'none',
                    'description' => 'All Systems Operational',
                ],
            ]),
        ]);

        $summary = $this->service->summary();

        expect($summary->isOperational())->toBeTrue();
    });

    it('returns false when there are incidents', function () {
        Http::fake([
            $this->endpoint => Http::response([
                'page' => $this->page,
                'status' => [
                    'indicator' => 'major',
                    'description' => 'Major Service Outage',
                ],
            ], 200),
        ]);

        $summary = $this->service->summary();

        expect($summary->isOperational())->toBeFalse();
    });
});

describe('status', function () {
    it('returns the current status indicator', function () {
        Http::fake([
            $this->endpoint => Http::response([
                'page' => $this->page,
                'status' => [
                    'indicator' => 'minor',
                    'description' => 'Minor Service Outage',
                ],
            ], 200),
        ]);

        $summary = $this->service->summary();

        expect($summary->status())->toBe(SummaryStatus::Minor);
    });
});

describe('description', function () {
    it('returns the current status description', function () {
        Http::fake([
            $this->endpoint => Http::response([
                'page' => $this->page,
                'status' => [
                    'indicator' => 'none',
                    'description' => 'All Systems Operational',
                ],
            ]),
        ]);

        $summary = $this->service->summary();

        expect($summary->description())->toBe('All Systems Operational');
    });
});

describe('hasIncidents', function () {
    it('returns true when there are active incidents', function () {
        Http::fake([
            $this->endpoint => Http::response([
                'page' => $this->page,
                'status' => [
                    'indicator' => 'critical',
                ],
                'incidents' => [
                    [
                        'id' => 'abc123',
                        'name' => 'API Issues',
                        'status' => 'investigating',
                    ],
                ],
            ]),
        ]);

        $summary = $this->service->summary();

        expect($summary->hasIncidents())->toBeTrue();
    });

    it('returns false when there are no incidents', function () {
        Http::fake([
            $this->endpoint => Http::response([
                'page' => $this->page,
                'status' => [
                    'indicator' => 'none',
                ],
                'incidents' => [],
            ]),
        ]);

        $summary = $this->service->summary();

        expect($summary->hasIncidents())->toBeFalse();
    });
});
