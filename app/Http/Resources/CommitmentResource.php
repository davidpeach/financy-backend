<?php

namespace App\Http\Resources;

use App\Utils\MoneyFormatter;
use Illuminate\Http\Resources\Json\JsonResource;

class CommitmentResource extends JsonResource
{
    public function toArray($request):array
    {
        return [
            'name' => $this->name,
            'amount' => MoneyFormatter::format($this->amount),
        ];
    }
}
