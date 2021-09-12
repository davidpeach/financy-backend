<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Commitment;
use App\Models\Payee;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        if (rand(0, 1)) {
            $recipient = Payee::factory()->create();
        } else {
            $recipient = Account::factory()->create();
        }

        return [
            'amount' => 10000,
            'date' => $this->faker->date,
            'commitment_id' => function () {
                return Commitment::factory()->create()->id;
            },
            'account_id' => function () {
                return Account::factory()->create()->id;
            },
            'name' => $this->faker->sentence,
            'closing_balance' => 10000,
            'recipient_id' => function () use ($recipient) {
                return $recipient->id;
            },
            'recipient_type' => function () use ($recipient) {
                return get_class($recipient);
            }
        ];
    }
}
