<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        return TransactionResource::collection(
            Transaction::orderBy('date', 'desc')->get()
        );
    }
}
