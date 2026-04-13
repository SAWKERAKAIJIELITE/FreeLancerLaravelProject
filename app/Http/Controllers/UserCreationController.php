<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequestRequest;
use App\Services\UserCreationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;

class UserCreationController extends Controller
{
    public function store(StoreAccountRequestRequest $request, UserCreationService $service): RedirectResponse
    {
        try {
            $service->create(Auth::user(), $request->validated());

            return back()->with('success', 'User created successfully.');
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', $exception->getMessage());
        }
    }
}
