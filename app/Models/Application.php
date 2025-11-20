<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'uses_https' => 'boolean',
        ];
    }

    /**
     * @return HasOne<Monitor, $this>
     */
    public function monitor(): HasOne
    {
        return $this->hasOne(Monitor::class);
    }
}
