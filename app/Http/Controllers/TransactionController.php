<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::orderBy('date', 'desc');

        if ($request->has('from')) {
            $transactions->where('date', '>=', $request->get('from'));
        }
        return TransactionResource::collection(
            $transactions->get()
        );
    }

    public function store(Request $request)
    {
        Transaction::create($request->all());
    }
}
