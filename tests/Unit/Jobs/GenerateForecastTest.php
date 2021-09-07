<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GenerateForecast;
use App\Models\Commitment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class GenerateForecastTest extends TestCase
{
    /** @test */
    public function it_generates_a_financial_forecast_based_on_financial_commitments_between_now_and_a_future_date()
    {
        Commitment::factory()
            ->count(2)
            ->state(new Sequence(
                ['name' => 'My £100 monthly commitment', 'amount' => 10000, 'recurring_date' => 20],
                ['name' => 'My £200 monthly commitment', 'amount' => 20000, 'recurring_date' => 10],
            ))
            ->create();

        $this->travelTo(new Carbon('11th January 2021'));

        GenerateForecast::dispatch(
            until: new Carbon('19th February 2021'),
        );

        $this->assertDatabaseHas('transactions', [
            'name' => 'My £100 monthly commitment',
            'amount' => 10000,
            'date' => (new Carbon('20th January 2021'))->format('Y-m-d H:i:s'),
        ])->assertDatabaseHas('transactions', [
            'name' => 'My £200 monthly commitment',
            'amount' => 20000,
            'date' => (new Carbon('10th February 2021'))->format('Y-m-d H:i:s'),
        ]);

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
