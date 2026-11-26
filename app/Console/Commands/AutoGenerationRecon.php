<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReconciliationController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class AutoGenerationRecon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recon:generate-recon';

    /**
     * The console command description.
     *
     * @var string
     */
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ReconciliationController $reconciliation)
    {
        $currentDateTime = Carbon::now()->setTimezone('Asia/Manila');
        $result = null;

        // Compare only hour:minute (not seconds): the scheduler/cron tick, PHP bootstrap,
        // and queueing jitter mean Carbon::now() is almost never exactly at second 0,
        // so matching the full "g:i:s A" string would (almost) never trigger.
        $currentTimeStr = $currentDateTime->format('g:i A'); // Output: '12:05 AM'
        $dayOfMonth = $currentDateTime->format('j');                    // Output: '17', '29', etc. (without leading zeros, or use 'd' for '17')
        $request = new Request([
            'cutoff' => 1
        ]);

        try {
            if ($currentTimeStr == "12:05 AM" && $dayOfMonth == "17") {
                $request->cutoff = 1;
                $result = $reconciliation->get_eprpo_data($request);
            }
            if ($currentTimeStr == "12:05 AM" && $dayOfMonth == "28") {
                $request->cutoff = 2;
                $result = $reconciliation->get_eprpo_data($request);
            }
        } catch (\Throwable $e) {
            $this->error('Expiration alert process failed: '.$e->getMessage());
            \Illuminate\Support\Facades\Log::error('AutoGenerationRecon failed', ['exception' => $e]);
            return Command::FAILURE;
        }

        $this->info('Starting license expiration alert process...');
        $this->info('Expiration alerts processed successfully. '.$result);
        return Command::SUCCESS;
    }
}
