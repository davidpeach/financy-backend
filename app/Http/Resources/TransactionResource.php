<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use NumberFormatter;

class TransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'amount' => $this->formatAsMoney($this->amount),
            'name' => $this->name,
            'date' => $this->date->format('jS F Y H:i:s'),
        ];
    }

    /**
     * @param int $amountInPennies
     * @return string
     */
    public function formatAsMoney(int $amountInPennies): string
    {
        $formatter = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amountInPennies/100, 'GBP');
    }
}
