<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Payment\WebhookProcessor;

class ProcessPendingWebhooks extends Command
{
    protected $signature = 'payment:process-webhooks {--limit=50 : Maximum number of webhooks to process}';
    protected $description = 'Process pending payment webhooks';

    public function __construct(
        private WebhookProcessor $webhookProcessor
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $this->info("Processando webhooks pendentes (limite: {$limit})...");

        $processed = $this->webhookProcessor->processPendingWebhooks($limit);

        $this->info("Processados {$processed} webhooks com sucesso.");

        return 0;
    }
}
