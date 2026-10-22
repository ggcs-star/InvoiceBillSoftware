<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Printer;
use App\Models\Restaurant;
use App\Models\User;
use App\Policies\AreaPolicy;
use App\Policies\BranchPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\MenuItemPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PrinterPolicy;
use App\Policies\RestaurantPolicy;
use App\Policies\StaffPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Restaurant::class => RestaurantPolicy::class,
        Branch::class => BranchPolicy::class,
        Area::class => AreaPolicy::class,
        Category::class => CategoryPolicy::class,
        MenuItem::class => MenuItemPolicy::class,
        Printer::class => PrinterPolicy::class,
        Order::class => OrderPolicy::class,
        Invoice::class => InvoicePolicy::class,
        User::class => StaffPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
