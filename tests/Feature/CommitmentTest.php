<?php

namespace Tests\Feature;

use App\Models\Commitment;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class CommitmentTest extends TestCase
{
    /** @test */
    public function new_commitments_can_be_retrieved_by_me()
    {
        Commitment::factory()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'My £100 commitment', 'amount' => 10000],
                ['name' => 'My £200 commitment', 'amount' => 20000],
                ['name' => 'My £300 commitment', 'amount' => 30000],
            ))
            ->create();

        $this->signIn();

        $response = $this->json('get', route('api.commitment.index'));

        $response->assertJson(['data' => [
            ['name' => 'My £100 commitment', 'amount' => '£100.00'],
            ['name' => 'My £200 commitment', 'amount' => '£200.00'],
            ['name' => 'My £300 commitment', 'amount' => '£300.00'],
        ]]);
    }
}
