<?php

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\Commitment;
use App\Models\Transaction;
use Tests\TestCase;

class AccountTest extends TestCase
{
    /** @test */
    public function it_can_transfer_money_to_another_account()
    {
        $this->withoutExceptionHandling();
        $billsAccount = Account::factory()->create(['name' => 'Bills Account', 'balance' => 50000]);
        $savingsAccount = Account::factory()->create(['name' => 'Savings Account', 'balance' => 0]);

        $billsAccount->transfer(20000, $savingsAccount);

        $this->assertEquals(30000, $billsAccount->fresh()->balance);
        $this->assertEquals(20000, $savingsAccount->fresh()->balance);
    }

    /** @test */
    public function an_account_can_have_transactions()
    {
        $billsAccount = Account::factory()->create();

        Transaction::factory()->count(3)->create(['account_id' => $billsAccount->id]);

        $this->assertCount(3, $billsAccount->transactions);
    }

    /** @test */
    public function an_account_can_have_commitments()
    {
        $billsAccount = Account::factory()->create();

        Commitment::factory()->count(3)->create(['account_id' => $billsAccount->id]);

        $this->assertCount(3, $billsAccount->commitments);
    }
}
