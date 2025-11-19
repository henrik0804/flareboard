<?php

namespace App\DataTransferObjects\Cloudflare;

use App\Enums\Cloudflare\ComponentStatus;
use App\Enums\Cloudflare\SummaryStatus;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;

/**
 * Represents the summary response from Cloudflare's status API.
 *
 * @property array<string, array{
 *      id: string,
 *      name: string,
 *      status: ComponentStatus
 *  }>|null $incidents
 */
final readonly class SummaryResponse
{
    public function __construct(
        public SummaryStatus $status,
        public CarbonInterface $time,
        public ?string $message = null,
        public ?array $incidents = null,
        public ?array $components = null,
        public ?array $scheduledMaintenances = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: SummaryStatus::from($data['status']['indicator']),
            time: Date::parse($data['page']['updated_at']),
            message: $data['status']['description'] ?? null,
            incidents: $data['incidents'] ?? null,
            components: $data['components'] ?? null,
            scheduledMaintenances: $data['scheduled_maintenances'] ?? null,
        );
    }

    public function isOperational(): bool
    {
        return $this->status === SummaryStatus::None;
    }

    public function description(): string
    {
        return $this->message ?? 'No description available.';
    }

    public function hasIncidents(): bool
    {
        return ! empty($this->incidents);
    }

    public function status(): SummaryStatus
    {
        return $this->status;
    }
}
