<?php

namespace App\Console\Commands;

use App\Actions\Cloudflare\RecordStatusAction;
use App\Services\Cloudflare\StatusService;
use Exception;
use Illuminate\Console\Command;

class SyncCloudflareStatusCommand extends Command
{
    protected $signature = 'sync:cloudflare-status';

    protected $description = 'Command description';

    public function handle(): void
    {
        try {
            $response = new StatusService()->status();
            dump($response);
            $model = new RecordStatusAction()->handle($response);
            dump($model);
        } catch (Exception $e) {
            $this->info('Cloudflare status sync failed: '.$e->getMessage());
        }
    }
}
