<?php

namespace App\Jobs;

use App\Models\Commitment;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateForecast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Carbon $until)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Transaction::where('date', '>', now())->delete();

        $latestBalance = Transaction::latest('date')->first()?->closing_balance;

        Commitment::orderBy('recurring_date', 'asc')
            ->get()
            ->each(function (Commitment $commitment) {
                $commitment->generateTransactionsUntil($this->until);
            });

        Transaction::where('date', '>', now())
            ->orderBy('date', 'asc')
            ->get()
            ->map(function (Transaction $transaction) use (&$latestBalance) {
                if ($transaction->isOutgoing()) {
                    $transaction->update([
                        'closing_balance' => $latestBalance -= $transaction->amount,
                    ]);
                } else {
                    $transaction->update([
                        'closing_balance' => $latestBalance += $transaction->amount,
                    ]);
                }
            });
    }
}
