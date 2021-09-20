<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Commitment;
use App\Models\Payee;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class TransactionsTest extends TestCase
{
    /** @test */
    public function account_transactions_can_be_returned_in_order_of_newest_first()
    {
        $this->signIn();

        $account = Account::factory()->create();

        $commitment = Commitment::factory()->create(['type' => 'OUTGOING']);

        Transaction::factory()
            ->count(5)
            ->state(new Sequence(
                [
                    'amount' => 1000,
                    'name' => '10 pound item',
                    'date' => new Carbon('10th January 2000 17:00:00'),
                    'closing_balance' => 10000,
                ],
                [
                    'amount' => 5000,
                    'name' => '50 pound item',
                    'date' => new Carbon('15th January 2000 17:00:00'),
                    'closing_balance' => 15000,
                ],
                [
                    'amount' => 2500,
                    'name' => '25 pound item',
                    'date' => new Carbon('1st January 2000 17:00:00'),
                    'closing_balance' => 1000,
                ],
                [
                    'amount' => 1000,
                    'name' => '10 pound item',
                    'date' => new Carbon('25th January 2000 17:00:00'),
                    'closing_balance' => 25000,
                ],
                [
                    'amount' => 7500,
                    'name' => '75 pound item',
                    'date' => new Carbon('20th January 2000 17:00:00'),
                    'closing_balance' => 20000,
                ],
            ))
            ->create([
                'account_id' => $account->id,
                'commitment_id' => $commitment->id,
            ]);

        Transaction::factory()->create([
            'amount' => 500,
            'name' => '5 pound item',
            'date' => new Carbon('5th January 2000 17:00:00'),
        ]);

        $response = $this->json('get', route('api.transaction.index', [$account]));

        $response->assertJson(['data' => [
            [
                'amount' => '£10.00',
                'name' => '10 pound item',
                'date' => '25th January 2000 17:00:00',
                'closing_balance' => '£250.00',
                'type' => 'OUTGOING',
            ],
            [
                'amount' => '£75.00',
                'name' => '75 pound item',
                'date' => '20th January 2000 17:00:00',
                'closing_balance' => '£200.00',
                'type' => 'OUTGOING',
            ],
            [
                'amount' => '£50.00',
                'name' => '50 pound item',
                'date' => '15th January 2000 17:00:00',
                'closing_balance' => '£150.00',
                'type' => 'OUTGOING',
            ],
            [
                'amount' => '£10.00',
                'name' => '10 pound item',
                'date' => '10th January 2000 17:00:00',
                'closing_balance' => '£100.00',
                'type' => 'OUTGOING',
            ],
            [
                'amount' => '£25.00',
                'name' => '25 pound item',
                'date' => '1st January 2000 17:00:00',
                'closing_balance' => '£10.00',
                'type' => 'OUTGOING',
            ],
        ]]);

        $response->assertJsonMissing([
            'amount' => '£5.00',
            'name' => '5 pound item',
            'date' => '5th January 2000 17:00:00'
        ]);
    }

    /** @test */
    public function transactions_cannot_be_returned_to_guests()
    {
        $response = $this->json('get', route('api.transaction.index', [Account::factory()->create()]));

        $response->assertStatus(401);
    }

    /** @test */
    public function single_transactions_can_be_created_manually()
    {
        $this->signIn();

        $account = Account::factory()->create();

        $recipient = Payee::factory()->create();

        $this->json('post', route('api.transaction.store', [$account]), [
            'amount' => '10.00',
            'name' => 'A 10 pound item',
            'date' => '2021-09-06 07:55:00',
            'recipient_id' => $recipient->id,
            'recipient_type' => get_class($recipient),
        ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => 1000,
            'name' => 'A 10 pound item',
            'date' => '2021-09-06 07:55:00',
            'account_id' => $account->id,
            'commitment_id' => null,
        ]);
    }

    /** @test */
    public function transactions_cannot_be_created_be_guests()
    {
        $account = Account::factory()->create();

        $response = $this->json('post', route('api.transaction.store', [$account]), [
            'amount' => '10.00',
            'name' => 'A 10 pound item',
            'date' => '2021-09-06 07:55:00',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function transactions_can_be_retrieved_based_on_a_start_date()
    {
        $this->signIn();

        $account = Account::factory()->create();

        Transaction::factory()
            ->count(3)
            ->state(new Sequence(
                ['amount' => 2500, 'name' => '25 pound item', 'date' => new Carbon('1st January 2000 17:00:00')],
                ['amount' => 7500, 'name' => '75 pound item', 'date' => new Carbon('5th January 2000 17:00:00')],
                ['amount' => 1000, 'name' => '10 pound item', 'date' => new Carbon('10th January 2000 17:00:00')],
            ))
            ->create(['account_id' => $account->id]);

        $response = $this->json('get', route('api.transaction.index', ['account' => $account, 'from' => '2000-01-05']));

        $response->assertJson(['data' => [
            ['amount' => '£10.00', 'name' => '10 pound item', 'date' => '10th January 2000 17:00:00'],
            ['amount' => '£75.00', 'name' => '75 pound item', 'date' => '5th January 2000 17:00:00'],
        ]]);
    }

}
