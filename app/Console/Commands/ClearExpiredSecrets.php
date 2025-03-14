<?php

namespace App\Console\Commands;

use App\Models\Secret;
use Illuminate\Console\Command;

class ClearExpiredSecrets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secrets:clear-expired-secrets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired secrets from the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing expired secrets...');

        $count = Secret::query()
            ->where('expired_at', '<=', now())
            ->delete();

        $this->info("{$count} expired secrets cleared.");
    }
}
