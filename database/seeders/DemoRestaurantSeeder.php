<?php

namespace Database\Seeders;

use App\Enums\PrinterConnectionType;
use App\Enums\UserRole;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * A ready-to-bill demo brand with four branches so the whole flow (branch ->
 * areas -> categories -> menu -> cart -> checkout -> invoice -> print) can be
 * exercised immediately after a fresh install. Prices differ per branch to
 * demonstrate that menu pricing is set independently at each location.
 */
class DemoRestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $restaurant = Restaurant::updateOrCreate(
            ['slug' => 'jagdai-cloud-kitchen'],
            ['name' => 'Jagdai Cloud Kitchen', 'cuisine_type' => 'Indian / Multi-Cuisine', 'is_active' => true]
        );

        $branches = [
            [
                'slug' => 'cg-road',
                'name' => 'CG Road',
                'address' => '1st Floor, CG Road, Navrangpura, Ahmedabad',
                'phone' => '+91 98765 43210',
                'gstin' => '24ABCDE1234F1Z1',
                'printer_ip' => '192.168.1.50',
                'price_multiplier' => 1.0,
                'cashier_email' => 'cashier@example.com',
            ],
            [
                'slug' => 'motera',
                'name' => 'Motera',
                'address' => 'Near Stadium Road, Motera, Ahmedabad',
                'phone' => '+91 98765 11223',
                'gstin' => '24ABCDE1234F1Z2',
                'printer_ip' => '192.168.1.60',
                'price_multiplier' => 1.05,
                'cashier_email' => 'cashier.motera@example.com',
            ],
            [
                'slug' => 'naranpura',
                'name' => 'Naranpura',
                'address' => 'Jawahar Chowk, Naranpura, Ahmedabad',
                'phone' => '+91 98765 33445',
                'gstin' => '24ABCDE1234F1Z3',
                'printer_ip' => '192.168.1.70',
                'price_multiplier' => 0.95,
                'cashier_email' => 'cashier.naranpura@example.com',
            ],
            [
                'slug' => 'gandhinagar',
                'name' => 'Gandhinagar',
                'address' => 'Sector 11, Gandhinagar',
                'phone' => '+91 98765 55667',
                'gstin' => '24ABCDE1234F1Z4',
                'printer_ip' => '192.168.1.80',
                'price_multiplier' => 1.1,
                'cashier_email' => 'cashier.gandhinagar@example.com',
            ],
        ];

        $menu = [
            'Pizzas' => [
                [
                    'name' => 'Margherita Pizza', 'price' => 249.00, 'gst' => 5,
                    'variations' => [
                        ['name' => 'Half', 'price' => 149.00],
                        ['name' => 'Full', 'price' => 249.00],
                    ],
                ],
                [
                    'name' => 'Farmhouse Pizza', 'price' => 329.00, 'gst' => 5,
                    'variations' => [
                        ['name' => 'Half', 'price' => 199.00],
                        ['name' => 'Full', 'price' => 329.00],
                    ],
                ],
            ],
            'Cold Drinks' => [
                ['name' => 'Cold Drink (330ml)', 'price' => 60.00, 'gst' => 12],
                ['name' => 'Fresh Lime Soda', 'price' => 80.00, 'gst' => 12],
            ],
            'Starters' => [
                ['name' => 'Paneer Tikka', 'price' => 220.00, 'gst' => 5],
            ],
        ];

        foreach ($branches as $branchData) {
            $branch = $restaurant->branches()->updateOrCreate(
                ['slug' => $branchData['slug']],
                [
                    'name' => $branchData['name'],
                    'address' => $branchData['address'],
                    'phone' => $branchData['phone'],
                    'gstin' => $branchData['gstin'],
                    'currency' => 'INR',
                    'is_active' => true,
                ]
            );

            $branch->printers()->updateOrCreate(
                ['name' => 'Counter Printer'],
                [
                    'connection_type' => PrinterConnectionType::Network,
                    'ip_address' => $branchData['printer_ip'],
                    'port' => 9100,
                    'is_default' => true,
                    'is_active' => true,
                ]
            );

            foreach ($menu as $categoryName => $items) {
                $category = $branch->categories()->updateOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    ['name' => $categoryName, 'is_active' => true]
                );

                foreach ($items as $item) {
                    $menuItem = $branch->menuItems()->updateOrCreate(
                        ['slug' => Str::slug($item['name'])],
                        [
                            'category_id' => $category->id,
                            'name' => $item['name'],
                            'price' => round($item['price'] * $branchData['price_multiplier'], 2),
                            'gst_percentage' => $item['gst'],
                            'is_available' => true,
                        ]
                    );

                    foreach ($item['variations'] ?? [] as $sortOrder => $variation) {
                        $menuItem->variations()->updateOrCreate(
                            ['name' => $variation['name']],
                            [
                                'price' => round($variation['price'] * $branchData['price_multiplier'], 2),
                                'sort_order' => $sortOrder,
                                'is_active' => true,
                            ]
                        );
                    }
                }
            }

            User::updateOrCreate(
                ['email' => $branchData['cashier_email']],
                [
                    'name' => 'Counter Staff — ' . $branchData['name'],
                    'password' => Hash::make('12345678'),
                    'role' => UserRole::User,
                    'branch_id' => $branch->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
