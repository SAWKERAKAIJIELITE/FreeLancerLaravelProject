<?php

namespace App\Http\Controllers;

use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducatorController extends Controller
{
    public function index(Request $request)
    {
        $requests = $request->user()->referrals()
            ->latest()
            ->paginate(10);

        return view('educator.dashboard', compact('requests'));
    }
}
