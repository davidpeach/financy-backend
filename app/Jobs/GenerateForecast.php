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
        Commitment::orderBy('recurring_date', 'asc')
            ->get()
            ->each(function (Commitment $commitment) {

                $start = now();
                $position = new Carbon($start->timestamp);
                $position->day = $commitment->recurring_date;

                while ($position <= $this->until) {

                    if ($this->isBeforeStart($start, $position)) {
                        $position->addMonth();
                        continue;
                    }

                    Transaction::create([
                        'name' => $commitment->name,
                        'amount' => $commitment->amount,
                        'date' => $position,
                    ]);

                    $position->addMonth();
                }
            });
    }

    protected function isBeforeStart(Carbon $start, Carbon $position): bool
    {
        return $position < $start;
    }
}
