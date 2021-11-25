<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

            if ($position > $this->end_date) {
                return;
            }

            $this->createTransaction($position);

            $position->addMonth();
        }
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function recipient()
    {
        return $this->morphTo();
    }

    private function createTransaction(Carbon $date)
    {
        $this->transactions()->save(
            Transaction::make([
                'name' => $this->name,
                'amount' => $this->amount,
                'date' => $date,
                'account_id' => $this->account->id,
                'recipient_id' => $this->recipient_id,
                'recipient_type' => $this->recipient_type,
            ])
        );
    }
}
