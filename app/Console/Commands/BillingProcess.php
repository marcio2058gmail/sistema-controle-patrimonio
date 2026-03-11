<?php

namespace App\Console\Commands;

use App\Services\Billing\BillingService;
use Illuminate\Console\Command;

/**
 * Comando diário de processamento de cobranças.
 *
 * Execução:
 *   php artisan billing:process
 *   php artisan billing:process --overdue-days=7 --suspend-days=45
 *
 * Agendar no Scheduler (app/Console/Kernel.php ou bootstrap/app.php):
 *   $schedule->command('billing:process')->dailyAt('06:00');
 */
class BillingProcess extends Command
{
    protected $signature = 'billing:process
                            {--overdue-days=5    : Dias após vencimento para marcar assinatura como overdue}
                            {--suspend-days=30   : Dias após vencimento para suspender empresa}';

    protected $description = 'Processa cobranças: gera faturas, marca inadimplentes e suspende empresas';

    public function __construct(
        private readonly BillingService $billingService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $overdueDays  = (int) $this->option('overdue-days');
        $suspendDays  = (int) $this->option('suspend-days');

        $this->info("🔄 Iniciando processamento de cobranças...");
        $this->newLine();

        $result = $this->billingService->process($overdueDays, $suspendDays);

        $this->table(
            ['Operação', 'Quantidade'],
            [
                ['Faturas geradas',               $result['invoices_generated']],
                ['Faturas marcadas como vencidas', $result['invoices_overdue']],
                ['Assinaturas marcadas overdue',   $result['subscriptions_overdue']],
                ['Empresas suspensas',             $result['subscriptions_suspended']],
            ]
        );

        $this->newLine();
        $this->info('✅ Processamento concluído.');

        return self::SUCCESS;
    }
}
