<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Branch;
use Illuminate\Http\Request;

/**
 * Branch-staff logins reach these same admin-style pages for their own
 * branch only. Route model binding resolves {branch} from the URL without
 * regard to who's asking, so this is the one place that stops a branch
 * account from typing another branch's id into the address bar.
 */
trait AuthorizesBranchAccess
{
    protected function authorizeBranch(Request $request, Branch $branch): void
    {
        abort_unless(
            $request->user()->isAdmin() || $request->user()->branch_id === $branch->id,
            403
        );
    }
}
