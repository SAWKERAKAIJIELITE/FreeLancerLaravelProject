<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


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
