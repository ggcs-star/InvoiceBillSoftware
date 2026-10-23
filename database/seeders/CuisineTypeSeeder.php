<?php

namespace Database\Seeders;

use App\Models\CuisineType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CuisineTypeSeeder extends Seeder
{
    public function run(): void
    {
        $cuisines = [
            'Indian / Multi-Cuisine',
            'Italian / Pizza',
            'Chinese',
            'Fast Food / Burgers',
            'Mexican',
            'Continental',
            'South Indian',
            'North Indian',
            'Bakery & Desserts',
            'Beverages & Cafe',
        ];

        foreach ($cuisines as $name) {
            CuisineType::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
