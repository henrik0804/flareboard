<?php

use App\DataTransferObjects\Cloudflare\StatusResponse;
use App\DataTransferObjects\Cloudflare\SummaryResponse;
use App\DataTransferObjects\Cloudflare\UnresolvedIncidentsResponse;
use App\Enums\Cloudflare\SummaryStatus;
use App\Services\Cloudflare\StatusService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->summaryEndpoint = 'https://www.cloudflarestatus.com/api/v2/summary.json';
    $this->statusEndpoint = 'https://www.cloudflarestatus.com/api/v2/status.json';
    $this->incidentsEndpoint = 'https://www.cloudflarestatus.com/api/v2/incidents/unresolved.json';
    $this->service = app(StatusService::class);
    $this->page = [
        'id' => 'yh6f0r4529hc',
        'name' => 'Cloudflare',
        'url' => 'https://www.cloudflarestatus.com',
        'updated_at' => Date::now(),
    ];
});

it('retrieves the summary from Cloudflare status API', function () {
    Http::fake([
        $this->summaryEndpoint => Http::response([
            'page' => $this->page,
            'status' => [
                'indicator' => 'none',
                'description' => 'All Systems Operational',
            ],
        ]),
    ]);

    $summary = $this->service->summary();

    expect($summary)
        ->toBeInstanceOf(SummaryResponse::class)
        ->and($summary->status)->toBe(SummaryStatus::None)
        ->and($summary->message)->toBe('All Systems Operational');
});

it('throws an exception when the summary API request fails', function () {
    Http::fake([
        $this->summaryEndpoint => Http::response([], 500),
    ]);

    $this->service->summary();
})->throws(RequestException::class);

it('throws an exception when the summary API is unreachable', function () {
    Http::fake([
        $this->summaryEndpoint => Http::response([], 404),
    ]);

    $this->service->summary();
})->throws(RequestException::class);

it('returns true when all systems are operational', function () {
    Http::fake([
        $this->summaryEndpoint => Http::response([
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
        $this->summaryEndpoint => Http::response([
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

it('returns the current status indicator', function () {
    Http::fake([
        $this->summaryEndpoint => Http::response([
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

it('returns the current status description', function () {
    Http::fake([
        $this->summaryEndpoint => Http::response([
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

it('returns true when there are active incidents', function () {
    Http::fake([
        $this->summaryEndpoint => Http::response([
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
        $this->summaryEndpoint => Http::response([
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

it('retrieves the status from Cloudflare status API', function () {
    Http::fake([
        $this->statusEndpoint => Http::response([
            'page' => $this->page,
            'status' => [
                'indicator' => 'none',
                'description' => 'All Systems Operational',
            ],
        ]),
    ]);

    $status = $this->service->status();

    expect($status)
        ->toBeInstanceOf(StatusResponse::class)
        ->and($status->status())->toBe(SummaryStatus::None)
        ->and($status->description())->toBe('All Systems Operational');
});

it('throws an exception when the status API request fails', function () {
    Http::fake([
        $this->statusEndpoint => Http::response([], 500),
    ]);

    $this->service->status();
})->throws(RequestException::class);

it('throws an exception when the status API is unreachable', function () {
    Http::fake([
        $this->statusEndpoint => Http::response([], 404),
    ]);

    $this->service->status();
})->throws(RequestException::class);

it('retrieves unresolved incidents from Cloudflare status API', function () {
    Http::fake([
        $this->incidentsEndpoint => Http::response([
            'page' => $this->page,
            'incidents' => [
                [
                    'id' => 'cp306tmzcl0y',
                    'name' => 'Unplanned Database Outage',
                    'status' => 'identified',
                    'impact' => 'critical',
                    'created_at' => '2014-05-14T14:22:39.441-06:00',
                    'updated_at' => '2014-05-14T14:35:21.711-06:00',
                    'incident_updates' => [
                        [
                            'body' => 'Our master database has ham sandwiches flying out of the rack.',
                            'created_at' => '2014-05-14T14:22:40.301-06:00',
                            'status' => 'identified',
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $response = $this->service->unresolvedIncidents();

    expect($response)
        ->toBeInstanceOf(UnresolvedIncidentsResponse::class)
        ->and($response->hasIncidents())->toBeTrue()
        ->and($response->incidents)->toHaveCount(1)
        ->and($response->incidents->first()->name)->toBe('Unplanned Database Outage')
        ->and($response->incidents->first()->impact)->toBe('critical');
});

it('returns no incidents when there are no unresolved incidents', function () {
    Http::fake([
        $this->incidentsEndpoint => Http::response([
            'page' => $this->page,
            'incidents' => [],
        ]),
    ]);

    $response = $this->service->unresolvedIncidents();

    expect($response)
        ->toBeInstanceOf(UnresolvedIncidentsResponse::class)
        ->and($response->hasIncidents())->toBeFalse()
        ->and($response->incidents)->toBeEmpty();
});

it('detects critical incidents correctly', function () {
    Http::fake([
        $this->incidentsEndpoint => Http::response([
            'page' => $this->page,
            'incidents' => [
                [
                    'id' => 'abc123',
                    'name' => 'Critical Issue',
                    'status' => 'investigating',
                    'impact' => 'critical',
                    'created_at' => Date::now()->toIso8601String(),
                    'updated_at' => Date::now()->toIso8601String(),
                    'incident_updates' => [],
                ],
            ],
        ]),
    ]);

    $response = $this->service->unresolvedIncidents();

    expect($response->hasCriticalIncidents())->toBeTrue()
        ->and($response->incidents->first()->isCritical())->toBeTrue();
});

it('returns false for critical incidents when all incidents are non-critical', function () {
    Http::fake([
        $this->incidentsEndpoint => Http::response([
            'page' => $this->page,
            'incidents' => [
                [
                    'id' => 'abc123',
                    'name' => 'Minor Issue',
                    'status' => 'investigating',
                    'impact' => 'minor',
                    'created_at' => Date::now()->toIso8601String(),
                    'updated_at' => Date::now()->toIso8601String(),
                    'incident_updates' => [],
                ],
            ],
        ]),
    ]);

    $response = $this->service->unresolvedIncidents();

    expect($response->hasCriticalIncidents())->toBeFalse()
        ->and($response->incidents->first()->isCritical())->toBeFalse();
});

it('parses incident updates correctly', function () {
    Http::fake([
        $this->incidentsEndpoint => Http::response([
            'page' => $this->page,
            'incidents' => [
                [
                    'id' => 'test123',
                    'name' => 'API Issues',
                    'status' => 'monitoring',
                    'impact' => 'major',
                    'created_at' => Date::now()->toIso8601String(),
                    'updated_at' => Date::now()->toIso8601String(),
                    'incident_updates' => [
                        [
                            'body' => 'We are monitoring the situation.',
                            'created_at' => Date::now()->toIso8601String(),
                            'status' => 'monitoring',
                        ],
                        [
                            'body' => 'Issue has been identified.',
                            'created_at' => Date::now()->subMinutes(5)->toIso8601String(),
                            'status' => 'identified',
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $response = $this->service->unresolvedIncidents();
    $incident = $response->incidents->first();

    expect($incident->latestUpdateBody)->toBe('We are monitoring the situation.');
});

it('throws an exception when the unresolved incidents API request fails', function () {
    Http::fake([
        $this->incidentsEndpoint => Http::response([], 500),
    ]);

    $this->service->unresolvedIncidents();
})->throws(RequestException::class);

it('throws an exception when the unresolved incidents API is unreachable', function () {
    Http::fake([
        $this->incidentsEndpoint => Http::response([], 404),
    ]);

    $this->service->unresolvedIncidents();
})->throws(RequestException::class);
