<?php

namespace Tests\Feature;

use App\Models\Commitment;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class ForecastShowCommandTest extends TestCase
{
    /** @test */
    public function it_generates_the_correct_forecast_for_future_transactions_when_calling_the_command()
    {
        $commitment = Commitment::factory()->create(['type' => 'OUTGOING']);

        $this->travelTo(new Carbon('2nd January 2000 17:00:00'));

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
                'commitment_id' => $commitment->id,
            ]);

        $command = $this->artisan('financy:forecast:show');

        $command->expectsTable([
            'Amount',
            'Name',
            'Date',
            'Closing Balance',
            'Type',
        ], [
            [
                '£10.00',
                '10 pound item',
                '10th January 2000 17:00:00',
                'closing_balance' => '£100.00',
                'type' => 'OUTGOING',
            ],
            [
                '£50.00',
                '50 pound item',
                '15th January 2000 17:00:00',
                'closing_balance' => '£150.00',
                'type' => 'OUTGOING',
            ],
            [
                '£75.00',
                '75 pound item',
                '20th January 2000 17:00:00',
                'closing_balance' => '£200.00',
                'type' => 'OUTGOING',
            ],
            [
                '£10.00',
                '10 pound item',
                '25th January 2000 17:00:00',
                'closing_balance' => '£250.00',
                'type' => 'OUTGOING',
            ],
        ]);
    }
}
