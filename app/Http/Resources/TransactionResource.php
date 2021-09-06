<?php

namespace App\Http\Resources;

use App\Utils\MoneyFormatter;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'amount' => MoneyFormatter::format($this->amount),
            'name' => $this->name,
            'date' => $this->date->format('jS F Y H:i:s'),
        ];
    }
}
