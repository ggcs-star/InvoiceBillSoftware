<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Models\Branch;
use App\Services\InvoiceService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Database\Seeder;

/**
 * Generates a month of realistic completed orders for each demo branch so
 * the admin dashboard and recent-transactions list have real data to render
 * instead of an empty state.
 */
class DemoOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::whereHas('restaurant', fn ($query) => $query->where('slug', 'spice-garden'))
            ->with('staff', 'menuItems')
            ->get();

        if ($branches->isEmpty()) {
            return;
        }

        $orders = app(OrderService::class);
        $payments = app(PaymentService::class);
        $invoices = app(InvoiceService::class);
        $methods = PaymentMethod::cases();

        foreach ($branches as $branch) {
            $cashier = $branch->staff->first();
            $menuItems = $branch->menuItems;

            if (! $cashier || $menuItems->isEmpty()) {
                continue;
            }

            for ($daysAgo = 29; $daysAgo >= 0; $daysAgo--) {
                $day = now()->subDays($daysAgo);
                $ordersToday = random_int(1, 5);

                for ($i = 0; $i < $ordersToday; $i++) {
                    $timestamp = $day->copy()->setTime(random_int(11, 21), random_int(0, 59));

                    $order = $orders->start($branch, $cashier);

                    foreach ($menuItems->random(random_int(1, 3)) as $menuItem) {
                        $orders->addItem($order, $menuItem, random_int(1, 3));
                    }

                    $payments->record($order->fresh(), $methods[array_rand($methods)], (string) $order->fresh()->grand_total);
                    $invoice = $invoices->generate($order->fresh());

                    $order->forceFill(['created_at' => $timestamp, 'updated_at' => $timestamp, 'completed_at' => $timestamp])->save();
                    $invoice->forceFill(['created_at' => $timestamp, 'updated_at' => $timestamp])->save();
                }
            }
        }
    }
}
