<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    public function transfer(int $amount, Account $targetAccount)
    {
        $this->balance -= $amount;
        $this->save();

        $targetAccount->balance += $amount;
        $targetAccount->save();

        $this->createTransaction(now(), $amount, $targetAccount);
    }

    protected function createTransaction(Carbon $date, $amount, $target)
    {
        $this->transactions()->save(
            Transaction::make([
                'name' => $this->name . ' -> ' . $target->name,
                'amount' => $amount,
                'date' => $date,
                'recipient_id' => $target->id,
                'recipient_type' => get_class($target),
            ])
        );
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
