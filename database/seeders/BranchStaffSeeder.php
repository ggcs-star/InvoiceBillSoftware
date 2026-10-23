<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * There is no self-registration in this app — every branch-wise login is
 * created here. Any branch that doesn't yet have a staff account gets one,
 * with the email derived from its restaurant + branch name and a fixed
 * password, so each branch's credentials only ever surface that branch's
 * own data via BranchScope.
 */
class BranchStaffSeeder extends Seeder
{
    public function run(): void
    {
        Branch::with('restaurant')->get()->each(function (Branch $branch) {
            if ($branch->staff()->exists()) {
                return;
            }

            $email = $this->uniqueEmail($branch);

            User::create([
                'name' => $branch->name . ' Staff',
                'email' => $email,
                'password' => Hash::make('12345678'),
                'role' => UserRole::User,
                'branch_id' => $branch->id,
                'is_active' => true,
            ]);
        });
    }

    private function uniqueEmail(Branch $branch): string
    {
        $base = Str::slug($branch->name) . '@' . Str::slug($branch->restaurant->name) . '.com';

        if (! User::where('email', $base)->exists()) {
            return $base;
        }

        $suffix = 2;
        do {
            $candidate = Str::slug($branch->name) . '-' . $suffix . '@' . Str::slug($branch->restaurant->name) . '.com';
            $suffix++;
        } while (User::where('email', $candidate)->exists());

        return $candidate;
    }
}
