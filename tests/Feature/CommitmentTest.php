<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Commitment;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class CommitmentTest extends TestCase
{
    /** @test */
    public function account_commitments_can_be_retrieved_by_me()
    {
        $account = Account::factory()->create();

        Commitment::factory()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'My £100 commitment', 'amount' => 10000],
                ['name' => 'My £200 commitment', 'amount' => 20000],
                ['name' => 'My £300 commitment', 'amount' => 30000],
            ))
            ->create(['account_id' => $account->id]);

        $otherAccountCommitment = Commitment::factory()->create([
            'name' => 'Another account commitment',
            'amount' => 50000,
        ]);

        $this->signIn();

        $response = $this->json('get', route('api.commitment.index', [$account]));

        $response->assertJson(['data' => [
            ['name' => 'My £100 commitment', 'amount' => '£100.00'],
            ['name' => 'My £200 commitment', 'amount' => '£200.00'],
            ['name' => 'My £300 commitment', 'amount' => '£300.00'],
        ]]);

        $response->assertJsonMissing([
            'name' => 'Another account commitment',
            'amount' => '£500',
        ]);
    }
}
