<?php

namespace App\Console\Commands;

use App\CatalogModule\Models\Transaction;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Actions\CaptureTabbyPayment;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CaptureTabbyPendingTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:capture-pending-transactions {--hours=24 : Hours to look back for pending transactions} {--dry-run : Show what would be captured without actually capturing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture Tabby pending transactions that haven\'t been captured yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $dryRun = $this->option('dry-run');
        
        \Log::info("Looking for Tabby pending transactions from the last {$hours} hours...");
        
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No actual captures will be performed');
        }

        // Find pending Tabby transactions
        $pendingTransactions = Transaction::where('status', ReservationPaymentStatus::PENDING->value)
            ->where('created_at', '>=', now()->subHours($hours))
            ->where('meta_data->gateway', 'tabby')
            ->whereNotNull('meta_data->invoiceId')
            ->get();
        

        if ($pendingTransactions->isEmpty()) {
            $this->info('No pending Tabby transactions found.');
            return 0;
        }

        $capturedCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($pendingTransactions as $transaction) {
            
            
            // Check if transaction is still valid for capture
            if (!$this->isTransactionValidForCapture($transaction)) {
                $this->warn("  - Skipping: Transaction not valid for capture");
                $skippedCount++;
                continue;
            }

            if ($dryRun) {
                $this->info("  - Would capture transaction ID: {$transaction->id}");
                $capturedCount++;
                continue;
            }

            try {
                
                
                // Attempt to capture the payment
                $result = CaptureTabbyPayment::run($transaction);
                
                if ($result->getStatusCode() === 200) {
                    
                    
                    $capturedCount++;
                    
                    \Log::info('Tabby transaction captured via command', [
                        'transaction_id' => $transaction->id,
                        'reservation_id' => $transaction->transactionable_id,
                        'user_id' => $transaction->user_id,
                        'amount' => $transaction->price->format(),
                        'captured_at' => now()->toIso8601String()
                    ]);
                } else {
                    $this->error("  - Failed to capture transaction ID: {$transaction->id}");
                    $failedCount++;
                    
                    \Log::error('Tabby transaction capture failed via command', [
                        'transaction_id' => $transaction->id,
                        'reservation_id' => $transaction->transactionable_id,
                        'user_id' => $transaction->user_id,
                        'response' => $result->getData()
                    ]);
                }
                
            } catch (\Exception $e) {
                $this->error("  - Exception capturing transaction ID: {$transaction->id} - {$e->getMessage()}");
                $failedCount++;
                
                \Log::error('Tabby transaction capture exception via command', [
                    'transaction_id' => $transaction->id,
                    'reservation_id' => $transaction->transactionable_id,
                    'user_id' => $transaction->user_id,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Summary
        \Log::info("=== CAPTURE SUMMARY ===");
        \Log::info("Total pending transactions found: {$pendingTransactions->count()}");
        \Log::info("Successfully captured: {$capturedCount}");
        \Log::info("Failed to capture: {$failedCount}");
        \Log::info("Skipped: {$skippedCount}");
        
        if ($dryRun) {
            $this->warn('DRY RUN COMPLETED - No actual captures were performed');
        }

        return 0;
    }

    /**
     * Check if a transaction is valid for capture
     */
    private function isTransactionValidForCapture(Transaction $transaction): bool
    {
        // Check if transaction has a valid Tabby invoice ID
        if (empty($transaction->meta_data['invoiceId'])) {
            return false;
        }

        // Check if the associated reservation exists and is in a valid state
        if (!$transaction->transactionable || !($transaction->transactionable instanceof Reservation)) {
            return false;
        }

        $reservation = $transaction->transactionable;
        
        // Only capture if reservation is still pending or processing
        if (!in_array($reservation->status, [
            ReservationStatus::PENDING,
            ReservationStatus::PROCESSING
        ])) {
            return false;
        }

        // Check if transaction is not too old (more than 7 days)
        if ($transaction->created_at->diffInDays(now()) > 7) {
            return false;
        }

        return true;
    }
} 