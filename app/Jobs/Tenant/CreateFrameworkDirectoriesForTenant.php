<?php

namespace App\Jobs\Tenant;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Contracts\Tenant;

class CreateFrameworkDirectoriesForTenant implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $tenant;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle()
    {
        $this->tenant->run(function ($tenant) {
            $storage_path = storage_path();

            if (Storage::directoryMissing("$storage_path/framework/cache")) {
                mkdir("$storage_path/framework/cache", 0777, true);
            }
        });
    }
}
