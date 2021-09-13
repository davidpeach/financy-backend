<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GenerateForecast;
use App\Models\Account;
use App\Models\Commitment;
use App\Models\Payee;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class GenerateForecastTest extends TestCase
{
    /** @test */
    public function it_regenerates_a_financial_forecast_based_on_financial_commitments_between_now_and_a_future_date()
    {
        // Given today is set
        $this->travelTo(new Carbon('11th January 2021'));

        // and we have an account
        $account = Account::factory()->create();

        // And we have a passed transaction from the day before
        $pastTransaction = Transaction::factory()
            ->create([
                'date' => (new Carbon('10th January 2021')),
                'commitment_id' => null,
                'name' => 'My Past Transaction',
                'closing_balance' => 100000,
                'account_id' => $account->id,
            ]);

        // and some future transaction
        $futureTransaction = Transaction::factory()
            ->create([
                'date' => now()->addDays(3),
                'commitment_id' => null,
                'name' => 'My Future Transaction',
                'account_id' => $account->id,
            ]);

        // And known payee commitments.
        $recipient100 = Payee::factory()->create();
        $recipient200 = Payee::factory()->create();
        Commitment::factory()
            ->count(2)
            ->state(new Sequence(
                [
                    'name' => 'My £200 monthly commitment',
                    'amount' => 20000,
                    'recurring_date' => 20,
                    'recipient_id' => $recipient200->id,
                    'recipient_type' => get_class($recipient200),
                ],
                [
                    'name' => 'My £100 monthly commitment',
                    'amount' => 10000,
                    'recurring_date' => 10,
                    'recipient_id' => $recipient100->id,
                    'recipient_type' => get_class($recipient100),
                ],
            ))
            ->create(['account_id' => $account->id]);

        // and an INCOMING wage
        Commitment::factory()
            ->create([
                'name' => 'My £2000 monthly wages',
                'amount' => 200000,
                'recurring_date' => 3,
                'recipient_id' => $account->id,
                'recipient_type' => get_class($account),
                'account_id' => $account->id,
                'type' => 'INCOMING',
            ]);


        // When generating a new forecast till a known date
        GenerateForecast::dispatch(
            until: new Carbon('19th February 2021'),
        );

        // The past transaction should still be saved
        $this->assertDatabaseHas('transactions', [
            'name' => $pastTransaction->name,
            'amount' => $pastTransaction->amount,
            'date' => $pastTransaction->date,
            'account_id' => $account->id,
            'closing_balance' => 100000,
        ]);

        // New transactions between now and until date are created.
        $this->assertDatabaseHas('transactions', [
            'name' => 'My £200 monthly commitment',
            'amount' => 20000,
            'date' => (new Carbon('20th January 2021'))->format('Y-m-d H:i:s'),
            'account_id' => $account->id,
            'closing_balance' => 80000,
        ])->assertDatabaseHas('transactions', [
            'name' => 'My £2000 monthly wages',
            'amount' => 200000,
            'date' => (new Carbon('3rd February 2021'))->format('Y-m-d H:i:s'),
            'account_id' => $account->id,
            'closing_balance' => 280000,
        ])->assertDatabaseHas('transactions', [
            'name' => 'My £100 monthly commitment',
            'amount' => 10000,
            'date' => (new Carbon('10th February 2021'))->format('Y-m-d H:i:s'),
            'account_id' => $account->id,
            'closing_balance' => 270000,
        ]);

        // The future transaction should now be gone
        // as it will be regenerated from known commitments.
        $this->assertDatabaseMissing('transactions', [
            'name' => $futureTransaction->name,
            'amount' => $futureTransaction->amount,
            'account_id' => $account->id,
            'date' => $futureTransaction->date,
        ]);

        // The commitment transactions outside of now and until date
        // should not be in the database
        $this->assertDatabaseMissing('transactions', [
            'name' => 'My £100 monthly commitment',
            'amount' => 10000,
            'account_id' => $account->id,
            'date' => (new Carbon('10th January 2021'))->format('Y-m-d H:i:s'),
        ])->assertDatabaseMissing('transactions', [
            'name' => 'My £200 monthly commitment',
            'amount' => 20000,
            'account_id' => $account->id,
            'date' => (new Carbon('20th February 2021'))->format('Y-m-d H:i:s'),
        ]);
    }

    /** @test */
    public function commitments_can_have_a_start_date_from_which_to_begin()
    {
        // Given we have a known today date
        $this->travelTo(new Carbon('1st January 2021'));
        // And we have a commitment that will start on a particular date
        $commitment = Commitment::factory()
            ->create([
                'recurring_date' => 15,
                'start_date' => new Carbon('15th February 2021')
            ]);

        // When we generate transactions
        GenerateForecast::dispatch(
            until: new Carbon('31st March 2021'),
        );

        // We should not see transaction for before the start date
        $this->assertDatabaseMissing('transactions', [
            'name' => $commitment->name,
            'amount' => $commitment->amount,
            'date' => (new Carbon('15th January 2021'))->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('transactions', [
            'name' => $commitment->name,
            'amount' => $commitment->amount,
            'date' => (new Carbon('15th February 2021'))->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('transactions', [
            'name' => $commitment->name,
            'amount' => $commitment->amount,
            'date' => (new Carbon('15th March 2021'))->format('Y-m-d H:i:s'),
        ]);
    }
}
