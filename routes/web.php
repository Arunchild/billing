<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
Route::resource('customers', \App\Http\Controllers\CustomerController::class);
Route::get('invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
Route::get('invoices/{id}/clone', [InvoiceController::class, 'clone'])->name('invoices.clone');
Route::get('invoices/{id}/items', [InvoiceController::class, 'getItems'])->name('invoices.items');
Route::post('invoices/bulk-destroy', [InvoiceController::class, 'bulkDestroy'])->name('invoices.bulk_destroy');
Route::resource('invoices', InvoiceController::class);

Route::get('sale_returns/{id}/print', [\App\Http\Controllers\SaleReturnController::class, 'print'])->name('sale_returns.print');
Route::resource('sale_returns', \App\Http\Controllers\SaleReturnController::class);

Route::get('quotations/{id}/print', [\App\Http\Controllers\QuotationController::class, 'print'])->name('quotations.print');
Route::get('quotations/{id}/clone', [\App\Http\Controllers\QuotationController::class, 'clone'])->name('quotations.clone');
Route::resource('quotations', \App\Http\Controllers\QuotationController::class);

Route::resource('onlinestore', \App\Http\Controllers\OnlineStoreController::class);

Route::get('purchases/{id}/items', [\App\Http\Controllers\PurchaseController::class, 'getItems'])->name('purchases.items');
Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);
Route::post('suppliers/ajax', [\App\Http\Controllers\SupplierController::class, 'storeAjax'])->name('suppliers.storeAjax');
Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

Route::get('purchase_orders/{id}/print', [\App\Http\Controllers\PurchaseOrderController::class, 'print'])->name('purchase_orders.print');
Route::resource('purchase_orders', \App\Http\Controllers\PurchaseOrderController::class);

Route::resource('debit_notes', \App\Http\Controllers\DebitNoteController::class);
Route::resource('credit_notes', \App\Http\Controllers\CreditNoteController::class);

Route::get('purchase_returns/{id}/print', [\App\Http\Controllers\PurchaseReturnController::class, 'print'])->name('purchase_returns.print');
Route::resource('purchase_returns', \App\Http\Controllers\PurchaseReturnController::class);
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
