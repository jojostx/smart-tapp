<?php

namespace App\Console\Commands;

use App\Models\Otp;
use Illuminate\Console\Command;

class ClearExpiredOtpsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean Otp table, remove all old otps that is expired or used.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Deleting expired otps...');

        $count = Otp::where('is_valid', false)
            ->where('created_at', '<', now()->subHour())
            ->delete();

        $this->comment("Deleted {$count} expired otps.");

        $this->info('All done!');
    }
}
