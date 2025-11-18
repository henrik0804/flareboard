<?php

namespace App\Services\Cloudflare;

use App\DataTransferObjects\CloudflareStatusResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class StatusService
{
    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function summary(): CloudflareStatusResponse
    {
        $response = Http::cloudflareStatus()->get('summary.json');

        $response->throw();

        return CloudflareStatusResponse::fromArray($response->json());
    }

}
