<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // $requests = User::where('referred_by', Auth::id())
        //     ->latest()
        //     ->paginate(10);
        $requests = $request->user()->referrals()
            ->latest()
            ->paginate(10);

        return view('user.dashboard', compact('requests'));
    }
}
