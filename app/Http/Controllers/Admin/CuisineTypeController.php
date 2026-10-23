<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuisineType;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CuisineTypeController extends Controller
{
    /**
     * Create a new cuisine type from the "+ Add Cuisine" popup on the
     * restaurant form, and return it so the dropdown can select it
     * immediately without a full page reload. Re-submitting a name that
     * already exists (same slug) returns the existing row instead of a
     * duplicate — firstOrCreate on the deterministic slug, not a freshly
     * uniqueified one, is what makes that match actually land.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Restaurant::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $cuisineType = CuisineType::firstOrCreate(
            ['slug' => Str::slug($data['name'])],
            ['name' => $data['name']]
        );

        return response()->json([
            'id' => $cuisineType->id,
            'name' => $cuisineType->name,
        ], 201);
    }
}
