<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

            Transaction::create([
                'name' => $this->name,
                'amount' => $this->amount,
                'date' => $position,
            ]);

            $position->addMonth();
        }
    }
}
