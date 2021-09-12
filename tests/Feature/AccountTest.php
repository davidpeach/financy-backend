<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use Tests\TestCase;

class AccountTest extends TestCase
{
    /** @test */
    public function i_can_transfer_money_from_one_account_to_another()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $billsAccount = Account::factory()->create(['name' => 'Bills Account', 'balance' => 50000]);
        $savingsAccount = Account::factory()->create(['name' => 'Savings Account', 'balance' => 0]);

        $url = vsprintf('/api/accounts/transfer?amount=%d&from=%s&to=%s', [
            20000,
            $billsAccount->id,
            $savingsAccount->id,
        ]);

        $this->json('post', $url);

        $this->assertEquals(30000, $billsAccount->fresh()->balance);
        $this->assertEquals(20000, $savingsAccount->fresh()->balance);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $billsAccount->id,
            'amount' => 20000,
            'name' => 'Bills Account -> Savings Account',
            'recipient_id' => $savingsAccount->id,
            'recipient_type' => 'App\Models\Account',
//            'date' => '2021-09-06 07:55:00',
        ]);

        $newTransaction = Transaction::first();

        $this->assertEquals($savingsAccount->id, $newTransaction->recipient->id);
    }
}
