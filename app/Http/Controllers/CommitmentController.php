<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommitmentResource;
use App\Models\Commitment;
use Illuminate\Http\Request;

class CommitmentController extends Controller
{
    public function index()
    {
        return CommitmentResource::collection(
            Commitment::all()
        );
    }
}
