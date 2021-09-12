<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'name',
        'date',
        'closing_balance',
        'account_id',
        'recipient_id',
        'recipient_type',
    ];

    protected $dates = ['date'];

    /**
     * @param string|int $amount
     * Handling if amount comes through in a '10.00' format.
     */
    public function setAmountAttribute(string|int $amount)
    {
        if (str_contains(haystack: $amount, needle: '.')) {
            $amount = (int) str_replace(search: '.', replace: '', subject: $amount);
        }

        $this->attributes['amount'] = $amount;
    }

    public function commitment(): BelongsTo
    {
        return $this->belongsTo(Commitment::class);
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }
}
