<?php

namespace App\Jobs;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessTermiiReportWebhook extends ProcessWebhookJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \logger($this->webhookCall);
    }
}
