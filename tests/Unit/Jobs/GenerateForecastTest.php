<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GenerateForecast;
use App\Models\Commitment;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class GenerateForecastTest extends TestCase
{
    /** @test */
    public function it_regenerates_a_financial_forecast_based_on_financial_commitments_between_now_and_a_future_date()
    {
        $this->travelTo(new Carbon('11th January 2021'));

        // Given we have a passed transaction
        $pastTransaction = Transaction::factory()
            ->create([
                'date' => now()->subDays(3),
                'commitment_id' => null,
                'name' => 'My Past Transaction',
            ]);

        // and a future transaction
        $futureTransaction = Transaction::factory()
            ->create([
                'date' => now()->addDays(3),
                'commitment_id' => null,
                'name' => 'My Future Transaction',
            ]);

        // And known commitments
        Commitment::factory()
            ->count(2)
            ->state(new Sequence(
                ['name' => 'My £100 monthly commitment', 'amount' => 10000, 'recurring_date' => 20],
                ['name' => 'My £200 monthly commitment', 'amount' => 20000, 'recurring_date' => 10],
            ))
            ->create();

        // When generating a new forecast till a known date
        GenerateForecast::dispatch(
            until: new Carbon('19th February 2021'),
        );

        // The past transaction should still be saved
        $this->assertDatabaseHas('transactions', [
            'name' => $pastTransaction->name,
            'amount' => $pastTransaction->amount,
            'date' => $pastTransaction->date,
        ]);

        // New transactions between now and until date are created.
        $this->assertDatabaseHas('transactions', [
            'name' => 'My £100 monthly commitment',
            'amount' => 10000,
            'date' => (new Carbon('20th January 2021'))->format('Y-m-d H:i:s'),
        ])->assertDatabaseHas('transactions', [
            'name' => 'My £200 monthly commitment',
            'amount' => 20000,
            'date' => (new Carbon('10th February 2021'))->format('Y-m-d H:i:s'),
        ]);

        // The future transaction should now be gone
        $this->assertDatabaseMissing('transactions', [
            'name' => $futureTransaction->name,
            'amount' => $futureTransaction->amount,
            'date' => $futureTransaction->date,
        ]);

        // The commitment transactions outside of now and until date
        // should not be in the database
        $this->assertDatabaseMissing('transactions', [
            'name' => 'My £200 monthly commitment',
            'amount' => 20000,
            'date' => (new Carbon('10th January 2021'))->format('Y-m-d H:i:s'),
        ])->assertDatabaseMissing('transactions', [
            'name' => 'My £100 monthly commitment',
            'amount' => 10000,
            'date' => (new Carbon('20th February 2021'))->format('Y-m-d H:i:s'),
        ]);
    }
}
