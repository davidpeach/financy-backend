<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Commitment;
use App\Models\Payee;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommitmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Commitment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if (rand(0, 1)) {
            $recipient = Payee::factory()->create();
        } else {
            $recipient = Account::factory()->create();
        }

        return [
            'name' => $this->faker->word,
            'amount' => 7777,
            'recurring_date' => 1,
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'account_id' => function () {
                return Account::factory()->create()->id;
            },
            'type' => 'OUTGOING',
            'recipient_id' => function () use ($recipient) {
                return $recipient->id;
            },
            'recipient_type' => function () use ($recipient) {
                return get_class($recipient);
            }
        ];
    }
}
