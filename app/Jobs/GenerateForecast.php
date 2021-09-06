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

    public Carbon $from;

    public Carbon $to;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Carbon $from, Carbon $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Commitment::orderBy('recurring_date', 'asc')
            ->get()
            ->each(function (Commitment $commitment) {

                $fromDate = new Carbon($this->from->timestamp);
//                $fromDate->startOfDay();
                $fromDate->day = $commitment->recurring_date;

                while ($fromDate <= $this->to) {

                    Transaction::create([
                        'name' => $commitment->name,
                        'amount' => $commitment->amount,
                        'date' => $fromDate,
                    ]);

                    $fromDate->addMonth();
                }
            });
    }
}
