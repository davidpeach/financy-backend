<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commitment extends Model
{
    use HasFactory;

    public function generateTransactionsUntil(Carbon $until)
    {
        $start = now();
        $position = new Carbon($start->timestamp);
        $position->day = $this->recurring_date;

        while ($position <= $until) {

            if ($position < $start) {
                $position->addMonth();
                continue;
            }

            if ($position < $this->start_date) {
                $position->addMonth();
                continue;
            }

            $this->createTransaction($position);

            $position->addMonth();
        }
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    private function createTransaction(Carbon $date)
    {
        $this->transactions()->save(
            Transaction::make([
                'name' => $this->name,
                'amount' => $this->amount,
                'date' => $date,
            ])
        );
    }
}
