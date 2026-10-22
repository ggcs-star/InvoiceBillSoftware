<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PrinterController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Billing\BillingController;
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

        Route::resource('restaurants', RestaurantController::class)->except('show');

        Route::prefix('restaurants/{restaurant}/branches')->name('branches.')->group(function () {
            Route::post('/', [BranchController::class, 'store'])->name('store');
            Route::get('/{branch}/edit', [BranchController::class, 'edit'])->name('edit');
            Route::put('/{branch}', [BranchController::class, 'update'])->name('update');
            Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('destroy');

            Route::prefix('/{branch}')->group(function () {
                Route::get('areas', [AreaController::class, 'index'])->name('areas.index');
                Route::post('areas', [AreaController::class, 'store'])->name('areas.store');
                Route::put('areas/{area}', [AreaController::class, 'update'])->name('areas.update');
                Route::delete('areas/{area}', [AreaController::class, 'destroy'])->name('areas.destroy');

                Route::get('printers', [PrinterController::class, 'index'])->name('printers.index');
                Route::post('printers', [PrinterController::class, 'store'])->name('printers.store');
                Route::put('printers/{printer}', [PrinterController::class, 'update'])->name('printers.update');
                Route::delete('printers/{printer}', [PrinterController::class, 'destroy'])->name('printers.destroy');

                Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
                Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
                Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
                Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

                Route::get('menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');
                Route::post('menu-items', [MenuItemController::class, 'store'])->name('menu-items.store');
                Route::put('menu-items/{menuItem}', [MenuItemController::class, 'update'])->name('menu-items.update');
                Route::patch('menu-items/{menuItem}/toggle', [MenuItemController::class, 'toggleAvailability'])->name('menu-items.toggle');
                Route::delete('menu-items/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy');

                Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');

                Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
                Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
                Route::put('staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
                Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
            });
        });
    });

    Route::middleware('role:user,admin')->group(function () {
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/orders/{order}', [BillingController::class, 'show'])->name('billing.orders.show');

        Route::get('/invoices', [InvoiceController::class, 'history'])->name('billing.invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('billing.invoices.show');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'printView'])->name('billing.invoices.print');
    });
});
