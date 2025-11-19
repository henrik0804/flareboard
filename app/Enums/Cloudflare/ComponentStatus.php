<?php

namespace App\Enums\Cloudflare;

enum ComponentStatus: string
{
    case Operational = 'operational';
    case DegradedPerformance = 'degraded_performance';
    case PartialOutage = 'partial_outage';
    case MajorOutage = 'major_outage';

    public function label(): string
    {
        return match ($this) {
            self::Operational => 'Operational',
            self::DegradedPerformance => 'Degraded Performance',
            self::PartialOutage => 'Partial Outage',
            self::MajorOutage => 'Major Outage',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Operational => 'bg-green-100 text-green-800',
            self::DegradedPerformance => 'bg-yellow-100 text-yellow-800',
            self::PartialOutage => 'bg-orange-100 text-orange-800',
            self::MajorOutage => 'bg-red-100 text-red-800',
        };
    }
}
