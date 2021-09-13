<?php

namespace Tests\Unit\Models;

use App\Models\Commitment;
use App\Models\Payee;
use App\Models\Transaction;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    /** @test */
    public function a_transaction_belongs_to_a_commitment()
    {
        $commitment = Commitment::factory()->create();
        $transaction = Transaction::factory()->create([
            'commitment_id' => $commitment->id,
        ]);

        $this->assertEquals($commitment->id, $transaction->commitment->id);
    }

    /** @test */
    public function a_one_off_transaction_can_have_a_recipient()
    {
        $recipient = Payee::factory()->create();

        $transaction = Transaction::factory()->create([
            'recipient_id' => $recipient->id,
            'recipient_type' => get_class($recipient),
        ]);

        $this->assertEquals($recipient->id, $transaction->recipient->id);
    }

    /** @test */
    public function a_transaction_knows_if_it_is_outgoing()
    {
        $commitment = Commitment::factory()->create([
            'type' => 'OUTGOING',
        ]);
        $transaction = Transaction::factory()->create(['commitment_id' => $commitment->id]);

        $this->assertTrue($transaction->isOutgoing());
    }

    /** @test */
    public function a_transaction_knows_if_it_is_incoming()
    {
        $commitment = Commitment::factory()->create([
            'type' => 'INCOMING',
        ]);
        $transaction = Transaction::factory()->create(['commitment_id' => $commitment->id]);

        $this->assertTrue($transaction->isIncoming());
    }
}
