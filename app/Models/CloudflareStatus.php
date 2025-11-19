<?php

namespace App\Models;

use App\Enums\Cloudflare\SummaryStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CloudflareStatus extends Model
{
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'updated_at_cloudflare' => 'datetime',
            'status' => SummaryStatus::class,
        ];
    }

    #[Scope]
    protected function current(Builder $query): Builder
    {
        return $query->where('ended_at', null);
    }
}
