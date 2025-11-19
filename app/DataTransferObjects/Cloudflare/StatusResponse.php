<?php

namespace App\DataTransferObjects\Cloudflare;

use App\Enums\Cloudflare\SummaryStatus;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;

final readonly class StatusResponse
{
    public function __construct(
        private SummaryStatus $status,
        private CarbonInterface $time,
        private ?string $message = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: SummaryStatus::from($data['status']['indicator']),
            time: Date::parse($data['page']['updated_at']),
            message: $data['status']['description'] ?? null,
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

    public function status(): SummaryStatus
    {
        return $this->status;
    }

    public function updatedAt()
    {
        return $this->time;
    }
}
