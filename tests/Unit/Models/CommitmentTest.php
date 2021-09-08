<?php

namespace Tests\Unit\Models;

use App\Models\Commitment;
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
}
