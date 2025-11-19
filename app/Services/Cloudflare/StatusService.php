<?php

namespace App\Services\Cloudflare;

use App\DataTransferObjects\Cloudflare\StatusResponse;
use App\DataTransferObjects\Cloudflare\SummaryResponse;
use App\DataTransferObjects\Cloudflare\UnresolvedIncidentsResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class StatusService
{
    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function summary(): SummaryResponse
    {
        $response = Http::cloudflareStatus()->get('summary.json');

        $response->throw();

        return SummaryResponse::fromArray($response->json());
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function status(): StatusResponse
    {
        $response = Http::cloudflareStatus()->get('status.json');

        $response->throw();

        return StatusResponse::fromArray($response->json());
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function unresolvedIncidents(): UnresolvedIncidentsResponse
    {
        $response = Http::cloudflareStatus()->get('incidents/unresolved.json');

        $response->throw();

        return UnresolvedIncidentsResponse::fromArray($response->json());
    }
}
