<?php

namespace App\Jobs\Tenant;

use Stancl\Tenancy\Jobs\DeleteDatabase;

class DeleteTenantDatabase extends DeleteDatabase
{
    public function handle()
    {
        $database = $this->tenant->database();

        if (!$database->manager()->databaseExists($database->getName())) {
           return;
        }
       
        parent::handle();
    }
}
