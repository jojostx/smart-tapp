<?php

namespace App\Console\Commands;

use App\Models\Otp;
use Illuminate\Console\Command;

class ClearExpiredOtps extends Command
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
        try {
            $otps = Otp::where('is_valid', false)->count();

            $this->info("Found {$otps} expired otps.");
            
            Otp::where('is_valid', false)->delete();
            
            $this->info("expired tokens deleted");
        } catch (\Exception $e) {
            $this->error("Error:: {$e->getMessage()}");
        }

        return 0;
    }
}
