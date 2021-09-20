<?php

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\Commitment;
use App\Models\Payee;
use App\Models\Recipient;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class CommitmentTest extends TestCase
{
    /** @test */
    public function it_can_have_transactions()
    {
        $commitment = Commitment::factory()->create();

        Transaction::factory()
            ->count(3)
            ->state([
                'amount' => $commitment->amount,
                'name' => $commitment->name,
                'commitment_id' => $commitment->id
            ])
            ->create();

        $this->assertCount(
            3,
            $commitment->transactions
        );
    }

    /** @test */
    public function a_commitment_belongs_to_an_account()
    {
        $account = Account::factory()->create();
        $commitment = Commitment::factory()->create([
            'account_id' => $account->id,
        ]);

        $this->assertEquals($account->id, $commitment->account->id);
    }

    /** @test */
    public function a_commitment_has_a_payee_recipient()
    {
        $account = Account::factory()->create();
        $recipient = Payee::factory()->create();
        $commitment = Commitment::factory()->create([
            'account_id' => $account->id,
            'recipient_id' => $recipient->id,
            'recipient_type' => get_class($recipient),
        ]);

        $this->assertEquals($recipient->id, $commitment->recipient->id);
    }

    /** @test */
    public function an_outgoing_commitment_has_an_account_recipient()
    {
        $billsAccount = Account::factory()->create();
        $savingsAccount = Account::factory()->create();
        $commitment = Commitment::factory()->create([
            'account_id' => $billsAccount->id,
            'recipient_id' => $savingsAccount->id,
            'recipient_type' => get_class($savingsAccount),
            'type' => 'OUTGOING',
        ]);

        $this->assertEquals($savingsAccount->id, $commitment->recipient->id);
    }

    /** @test */
    public function an_incoming_commitment_does_not_have_an_account_recipient()
    {
        $billsAccount = Account::factory()->create();
        $savingsAccount = Account::factory()->create();
        $commitment = Commitment::factory()->create([
            'account_id' => $billsAccount->id,
            'recipient_id' => null,
            'recipient_type' => null,
            'type' => 'INCOMING',
        ]);

        $this->assertNull($commitment->recipient);
    }
}
