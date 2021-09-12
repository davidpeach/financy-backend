<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class BalanceTransferController extends Controller
{
    public function store(Request $request)
    {
        $fromAccount = Account::find($request->from);
        $toAccount = Account::find($request->to);

        $fromAccount->transfer($request->amount, $toAccount);
    }
}
