<?php

namespace App\Actions;

use App\Models\Application;

final readonly class StoreApplicationAction
{
    public function handle(array $attributes): Application
    {
        return Application::create([
            'name' => $attributes['name'],
            'domain' => $attributes['domain'],
            'registrar' => $attributes['registrar'],
            'ns_provider' => $attributes['ns_provider'],
            'uses_https' => $attributes['uses_https'] ?? false,
        ]);
    }
}
