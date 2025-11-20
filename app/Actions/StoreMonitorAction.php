<?php

namespace App\Actions;

use App\Models\Application;
use App\Models\Monitor;

final readonly class StoreMonitorAction
{
    public function handle(Application $application): Monitor
    {
        return Monitor::create([
            'application_id' => $application->id,
            'url' => $this->buildUrl($application->domain, $application->uses_https),
            'uptime_check_enabled' => true,
            'look_for_string' => '',
        ]);
    }

    private function buildUrl(string $domain, bool $usesHttps): string
    {
        $protocol = $usesHttps ? 'https://' : 'http://';

        return $protocol.$domain;
    }
}
