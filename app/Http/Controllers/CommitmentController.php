<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommitmentResource;
use App\Models\Account;
use App\Models\Commitment;
use Illuminate\Http\Request;

class CommitmentController extends Controller
{
    public function index(Account $account)
    {
        return CommitmentResource::collection(
            $account->commitments->all()
        );
    }
}
