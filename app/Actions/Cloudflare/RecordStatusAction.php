<?php

namespace App\Actions\Cloudflare;

use App\DataTransferObjects\Cloudflare\StatusResponse;
use App\Models\CloudflareStatus;

class RecordStatusAction
{
    public function handle(StatusResponse $statusResponse): CloudflareStatus
    {
        $existing = CloudflareStatus::current()->first();
        dump($existing);
        if (! is_null($existing) && $existing->status === $statusResponse->status()) {
            $existing->update([
                'current_description' => $statusResponse->description(),
                'updated_at_cloudflare' => $statusResponse->updatedAt(),
            ]);

            return $existing;
        }

        return CloudflareStatus::create([
            'status' => $statusResponse->status(),
            'current_description' => $statusResponse->description(),
            'updated_at_cloudflare' => $statusResponse->updatedAt(),
            'started_at' => $statusResponse->updatedAt(),
        ]);
    }
}
