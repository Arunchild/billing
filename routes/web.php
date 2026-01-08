<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
Route::resource('customers', \App\Http\Controllers\CustomerController::class);
Route::resource('invoices', InvoiceController::class);
Route::resource('onlinestore', \App\Http\Controllers\OnlineStoreController::class);
Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);
Route::resource('inventory', \App\Http\Controllers\InventoryController::class);
Route::resource('accounts', \App\Http\Controllers\AccountController::class);
Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
Route::resource('staff', \App\Http\Controllers\StaffController::class);
Route::resource('tools', \App\Http\Controllers\ToolController::class);
Route::resource('master', \App\Http\Controllers\MasterController::class);
Route::resource('settings', \App\Http\Controllers\SettingController::class);
Route::resource('units', \App\Http\Controllers\UnitController::class);
Route::resource('tax_rates', \App\Http\Controllers\TaxRateController::class);
Route::resource('categories', \App\Http\Controllers\CategoryController::class);
Route::get('/accounts/statements', [\App\Http\Controllers\AccountController::class, 'statements'])->name('accounts.statements');
Route::post('/inventory/{id}/adjust', [\App\Http\Controllers\InventoryController::class, 'adjust'])->name('inventory.adjust');
Route::resource('products', \App\Http\Controllers\ProductController::class);
Route::resource('reminders', \App\Http\Controllers\ReminderController::class);
Route::get('/barcode/scanner', [\App\Http\Controllers\BarcodeController::class, 'scanner'])->name('barcode.scanner');
Route::post('/barcode/lookup', [\App\Http\Controllers\BarcodeController::class, 'lookup'])->name('barcode.lookup');
Route::get('/barcode/label/{id}', [\App\Http\Controllers\BarcodeController::class, 'printLabel'])->name('barcode.label');
