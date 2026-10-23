<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        $demoAdmin = User::where('role', UserRole::Admin)->oldest('id')->first();

        // Only surface branch logins that actually have a menu to explore —
        // an empty branch makes for a useless demo account.
        $demoStaff = User::with('branch')
            ->where('role', UserRole::User)
            ->whereHas('branch', fn ($query) => $query->whereHas('menuItems'))
            ->oldest('id')
            ->get();

        return view('auth.login', compact('demoAdmin', 'demoStaff'));
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = $request->user();

        $token = $user->createToken('web-session')->accessToken;
        $request->session()->put('api_token', $token);

        return redirect()->intended(
            $user->role === UserRole::Admin ? route('admin.dashboard') : route('billing.index')
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
