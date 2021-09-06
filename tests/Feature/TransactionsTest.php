<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function transactions_can_be_returned_in_order_of_newest_first()
    {
        $this->be(User::factory()->create());

        Transaction::factory()
            ->count(5)
            ->state(new Sequence(
                ['amount' => 1000, 'name' => '10 pound item', 'date' => new Carbon('10th January 2000 17:00:00')],
                ['amount' => 5000, 'name' => '50 pound item', 'date' => new Carbon('15th January 2000 17:00:00')],
                ['amount' => 2500, 'name' => '25 pound item', 'date' => new Carbon('1st January 2000 17:00:00')],
                ['amount' => 1000, 'name' => '10 pound item', 'date' => new Carbon('25th January 2000 17:00:00')],
                ['amount' => 7500, 'name' => '75 pound item', 'date' => new Carbon('20th January 2000 17:00:00')],
            ))
            ->create();

        $response = $this->json('get', route('api.transaction.index'));

        $response->assertJson(['data' => [
            ['amount' => '£10.00', 'name' => '10 pound item', 'date' => '25th January 2000 17:00:00'],
            ['amount' => '£75.00', 'name' => '75 pound item', 'date' => '20th January 2000 17:00:00'],
            ['amount' => '£50.00', 'name' => '50 pound item', 'date' => '15th January 2000 17:00:00'],
            ['amount' => '£10.00', 'name' => '10 pound item', 'date' => '10th January 2000 17:00:00'],
            ['amount' => '£25.00', 'name' => '25 pound item', 'date' => '1st January 2000 17:00:00'],
        ]]);
    }

    /** @test */
    public function transactions_cannot_be_returned_to_guests()
    {
        $response = $this->json('get', route('api.transaction.index'));

        $response->assertStatus(401);
    }

    /** @test */
    public function single_transactions_can_be_created_manually()
    {
        $this->be(User::factory()->create());

        $this->json('post', route('api.transaction.store'), [
            'amount' => '10.00',
            'name' => 'A 10 pound item',
            'date' => '2021-09-06 07:55:00',
        ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => 1000,
            'name' => 'A 10 pound item',
            'date' => '2021-09-06 07:55:00',
        ]);
    }

    /** @test */
    public function transactions_cannot_be_created_be_guests()
    {
        $response = $this->json('post', route('api.transaction.store'), [
            'amount' => '10.00',
            'name' => 'A 10 pound item',
            'date' => '2021-09-06 07:55:00',
        ]);

        $response->assertStatus(401);
    }

}
