<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaffRequest;
use App\Models\Branch;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request, Restaurant $restaurant, Branch $branch): View
    {
        $this->authorize('viewAny', User::class);

        $staff = $branch->staff()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'active'))
            ->paginate(10)
            ->withQueryString();

        return view('admin.branches.staff', compact('restaurant', 'branch', 'staff'));
    }

    public function store(StaffRequest $request, Restaurant $restaurant, Branch $branch): RedirectResponse
    {
        $this->authorize('create', User::class);

        $branch->staff()->create([
            ...$request->validated(),
            'password' => Hash::make($request->input('password')),
            'role' => UserRole::User,
        ]);

        return back()->with('status', 'Staff account created.');
    }

    public function update(StaffRequest $request, Restaurant $restaurant, Branch $branch, User $staff): RedirectResponse
    {
        $this->authorize('update', $staff);

        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $staff->update($data);

        return back()->with('status', 'Staff account updated.');
    }

    public function destroy(Restaurant $restaurant, Branch $branch, User $staff): RedirectResponse
    {
        $this->authorize('delete', $staff);

        $staff->delete();

        return back()->with('status', 'Staff account removed.');
    }
}
