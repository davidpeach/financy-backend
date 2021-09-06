<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return TransactionResource::collection(
            Transaction::orderBy('date', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        Transaction::create($request->all());
    }
}
