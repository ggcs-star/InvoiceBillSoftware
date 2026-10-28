<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CuisineTypeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\MenuItemImportController;
use App\Http\Controllers\Admin\MenuItemVariationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PrinterController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Billing\BillingController;
use App\Http\Controllers\Billing\DashboardController as StaffDashboardController;
use App\Http\Controllers\Billing\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('restaurants', RestaurantController::class);
        Route::post('cuisine-types', [CuisineTypeController::class, 'store'])->name('cuisine-types.store');

        Route::get('branches', [BranchController::class, 'all'])->name('branches.index');
        Route::get('categories', [CategoryController::class, 'all'])->name('categories.index');
        Route::get('menu-items', [MenuItemController::class, 'all'])->name('menu-items.index');
        Route::get('menu-items-export', [MenuItemController::class, 'export'])->name('menu-items.export');
        Route::get('menu-items/bulk-import', [MenuItemImportController::class, 'create'])->name('menu-items.bulk-import');
        Route::post('menu-items/bulk-import', [MenuItemImportController::class, 'store'])->name('menu-items.bulk-import.store');
        Route::get('menu-items/bulk-import/sample', [MenuItemImportController::class, 'sample'])->name('menu-items.bulk-import.sample');
        Route::get('orders', [AdminOrderController::class, 'all'])->name('orders.index');
        Route::get('printers', [PrinterController::class, 'all'])->name('printers.index');
        Route::get('staff', [StaffController::class, 'all'])->name('staff.index');

        Route::prefix('restaurants/{restaurant}/branches')->name('branches.')->group(function () {
            Route::post('/', [BranchController::class, 'store'])->name('store');
            Route::get('/{branch}', [BranchController::class, 'show'])->name('show');
            Route::get('/{branch}/edit', [BranchController::class, 'edit'])->name('edit');
            Route::put('/{branch}', [BranchController::class, 'update'])->name('update');
            Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('destroy');
        });
    });

    // Categories, Menu Items, and Orders for a branch are reachable by that
    // branch's own staff login too (scoped to their own branch — enforced in
    // the controllers, not just here), not only platform Admins.
    Route::middleware('role:admin,user')->prefix('admin')->name('admin.')->group(function () {
        Route::prefix('restaurants/{restaurant}/branches/{branch}')->name('branches.')->group(function () {
            Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

            Route::get('menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');
            Route::get('menu-items-export', [MenuItemController::class, 'exportForBranch'])->name('menu-items.export');
            Route::get('menu-items/bulk-import', [MenuItemImportController::class, 'createForBranch'])->name('menu-items.bulk-import');
            Route::post('menu-items/bulk-import', [MenuItemImportController::class, 'storeForBranch'])->name('menu-items.bulk-import.store');
            Route::get('menu-items/bulk-import/sample', [MenuItemImportController::class, 'sample'])->name('menu-items.bulk-import.sample');
            Route::post('menu-items', [MenuItemController::class, 'store'])->name('menu-items.store');
            Route::put('menu-items/{menuItem}', [MenuItemController::class, 'update'])->name('menu-items.update');
            Route::patch('menu-items/{menuItem}/toggle', [MenuItemController::class, 'toggleAvailability'])->name('menu-items.toggle');
            Route::patch('menu-items/{menuItem}/toggle-customer-site', [MenuItemController::class, 'toggleCustomerSite'])->name('menu-items.toggle-customer-site');
            Route::delete('menu-items/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy');

            Route::post('menu-items/{menuItem}/variations', [MenuItemVariationController::class, 'store'])->name('menu-items.variations.store');
            Route::put('menu-items/{menuItem}/variations/{variation}', [MenuItemVariationController::class, 'update'])->name('menu-items.variations.update');
            Route::delete('menu-items/{menuItem}/variations/{variation}', [MenuItemVariationController::class, 'destroy'])->name('menu-items.variations.destroy');

            Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');

            Route::get('printers', [PrinterController::class, 'index'])->name('printers.index');
            Route::post('printers', [PrinterController::class, 'store'])->name('printers.store');
            Route::put('printers/{printer}', [PrinterController::class, 'update'])->name('printers.update');
            Route::patch('printers/{printer}/approve', [PrinterController::class, 'approve'])->name('printers.approve');
            Route::delete('printers/{printer}', [PrinterController::class, 'destroy'])->name('printers.destroy');

            Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
            Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
            Route::put('staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
            Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
        });
    });

    Route::middleware('role:user')->group(function () {
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('billing.dashboard');
    });

    Route::middleware('role:user,admin')->group(function () {
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/orders/{order}', [BillingController::class, 'show'])->name('billing.orders.show');

        Route::get('/invoices', [InvoiceController::class, 'history'])->name('billing.invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('billing.invoices.show');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'printView'])->name('billing.invoices.print');
    });
});
