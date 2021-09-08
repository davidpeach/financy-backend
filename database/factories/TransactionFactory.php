<?php

namespace Database\Factories;

use App\Models\Commitment;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'date' => $this->faker->date,
            'commitment_id' => function () {
                return Commitment::factory()->create()->id;
            },
        ];
    }
}
