<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class ForecastGenerationCommandTest extends TestCase
{
    /** @test */
    public function it_generates_the_correct_forecast_when_calling_the_command()
    {
//        $this->withoutExceptionHandling();
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

        $command = $this->artisan('financy:forecast:show');

        $command->expectsTable([
            'Amount',
            'Name',
        ], [
            ['£25.00', '25 pound item', '1st January 2000 17:00:00'],
            ['£10.00', '10 pound item', '10th January 2000 17:00:00'],
            ['£50.00', '50 pound item', '15th January 2000 17:00:00'],
            ['£75.00', '75 pound item', '20th January 2000 17:00:00'],
            ['£10.00', '10 pound item', '25th January 2000 17:00:00'],
        ]);
    }
}
