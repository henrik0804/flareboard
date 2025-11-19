<?php

namespace App\DataTransferObjects\Cloudflare;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

final readonly class UnresolvedIncidentsResponse
{
    /**
     * @param  Collection<int, Incident>  $incidents
     */
    public function __construct(
        public CarbonInterface $pageUpdatedAt,
        public Collection $incidents,
    ) {}

    public static function fromArray(array $data): self
    {
        $incidents = collect($data['incidents'] ?? [])
            ->map(fn (array $incident) => Incident::fromArray($incident));

        return new self(
            pageUpdatedAt: Date::parse($data['page']['updated_at']),
            incidents: $incidents,
        );
    }

    public function hasIncidents(): bool
    {
        return $this->incidents->isNotEmpty();
    }

    public function hasCriticalIncidents(): bool
    {
        return $this->incidents->contains(fn (Incident $incident) => $incident->impact === 'critical');
    }
}

final readonly class Incident
{
    public function __construct(
        public string $id,
        public string $name,
        public string $status,
        public string $impact,
        public CarbonInterface $createdAt,
        public CarbonInterface $updatedAt,
        public ?string $latestUpdateBody = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $latestUpdate = $data['incident_updates'][0] ?? null;

        return new self(
            id: $data['id'],
            name: $data['name'],
            status: $data['status'],
            impact: $data['impact'],
            createdAt: Date::parse($data['created_at']),
            updatedAt: Date::parse($data['updated_at']),
            latestUpdateBody: $latestUpdate['body'] ?? null,
        );
    }

    public function isCritical(): bool
    {
        return $this->impact === 'critical';
    }
}
