<?php

namespace App\Enums\Cloudflare;

enum SummaryStatus: string
{
    case None = 'none';
    case Minor = 'minor';
    case Major = 'major';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Operational',
            self::Minor => 'Minor Issue',
            self::Major => 'Major Issue',
            self::Critical => 'Critical Failure',
        };
    }

    public function backgroundClass(): string
    {
        return match ($this) {
            self::None => 'bg-white dark:bg-neutral-900',
            self::Minor => 'bg-yellow-50 dark:bg-yellow-950/20',
            self::Major => 'bg-orange-50 dark:bg-orange-950/20',
            self::Critical => 'bg-red-50 dark:bg-red-950/20',
        };
    }

    /**
     * The border is now handled by a separate overlay div.
     * We use ring-inset to ensure it fits perfectly inside the container.
     */
    public function borderClass(): string
    {
        return match ($this) {
            self::None => '', // No border for normal state
            self::Minor => '',
            self::Major => 'ring-1 ring-inset ring-orange-200 dark:ring-orange-800',
            self::Critical => 'ring-2 ring-inset ring-red-500 dark:ring-red-600',
        };
    }

    public function iconClass(): string
    {
        return match ($this) {
            self::None => 'text-emerald-500 dark:text-emerald-400',
            self::Minor => 'text-yellow-500 dark:text-yellow-400',
            self::Major => 'text-orange-500 dark:text-orange-400',
            self::Critical => 'text-red-600 dark:text-red-500 animate-pulse',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::None => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
            self::Minor => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400 border-yellow-200 dark:border-yellow-500/20',
            self::Major => 'bg-orange-100 text-orange-800 dark:bg-orange-500/10 dark:text-orange-400 border-orange-200 dark:border-orange-500/20',
            self::Critical => 'bg-red-600 text-white border-red-700 dark:border-red-500 shadow-sm',
        };
    }

    public function pulseClass(): string
    {
        return match ($this) {
            self::None => 'bg-emerald-500',
            self::Minor => 'bg-yellow-500',
            self::Major => 'bg-orange-500',
            self::Critical => 'bg-white',
        };
    }
}
