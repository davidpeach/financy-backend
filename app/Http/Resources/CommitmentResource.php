<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use NumberFormatter;

class CommitmentResource extends JsonResource
{
    public function toArray($request):array
    {
        return [
            'name' => $this->name,
            'amount' => $this->formatAsMoney($this->amount),
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
