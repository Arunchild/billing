<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Product Details
            if (!Schema::hasColumn('products', 'group')) {
                $table->string('group')->nullable();
            }
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable();
            }
            if (!Schema::hasColumn('products', 'item_code')) {
                $table->string('item_code')->nullable()->unique();
            }
            if (!Schema::hasColumn('products', 'print_name')) {
                $table->string('print_name')->nullable();
            }
            
            // Price Details
            if (!Schema::hasColumn('products', 'purchase_price')) {
                $table->decimal('purchase_price', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'min_sale_price')) {
                $table->decimal('min_sale_price', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'mrp')) {
                $table->decimal('mrp', 10, 2)->default(0);
            }
            
            // Stock and Unit Details
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit')->nullable();
            }
            if (!Schema::hasColumn('products', 'opening_stock')) {
                $table->decimal('opening_stock', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'opening_stock_value')) {
                $table->decimal('opening_stock_value', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'current_stock')) {
                $table->decimal('current_stock', 10, 2)->default(0);
            }
            
            // GST Details
            if (!Schema::hasColumn('products', 'hsn_sac_code')) {
                $table->string('hsn_sac_code')->nullable();
            }
            if (!Schema::hasColumn('products', 'cgst_rate')) {
                $table->decimal('cgst_rate', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'sgst_rate')) {
                $table->decimal('sgst_rate', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'igst_rate')) {
                $table->decimal('igst_rate', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'cess_rate')) {
                $table->decimal('cess_rate', 5, 2)->default(0);
            }
            
            // Other Details
            if (!Schema::hasColumn('products', 'sale_discount')) {
                $table->decimal('sale_discount', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'low_level_limit')) {
                $table->integer('low_level_limit')->default(0);
            }
            if (!Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type')->default('General');
            }
            if (!Schema::hasColumn('products', 'location_rack')) {
                $table->string('location_rack')->nullable();
            }
            if (!Schema::hasColumn('products', 'serial_no')) {
                $table->string('serial_no')->nullable();
            }
            if (!Schema::hasColumn('products', 'product_description')) {
                $table->text('product_description')->nullable();
            }
            
            // Product Settings (Boolean flags)
            if (!Schema::hasColumn('products', 'print_description')) {
                $table->boolean('print_description')->default(false);
            }
            if (!Schema::hasColumn('products', 'print_serial_no')) {
                $table->boolean('print_serial_no')->default(false);
            }
            if (!Schema::hasColumn('products', 'one_click_sale')) {
                $table->boolean('one_click_sale')->default(false);
            }
            if (!Schema::hasColumn('products', 'not_for_sale')) {
                $table->boolean('not_for_sale')->default(false);
            }
            if (!Schema::hasColumn('products', 'enable_tracking')) {
                $table->boolean('enable_tracking')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'group', 'brand', 'item_code', 'print_name',
                'purchase_price', 'sale_price', 'min_sale_price', 'mrp',
                'unit', 'opening_stock', 'opening_stock_value', 'current_stock',
                'hsn_sac_code', 'cgst_rate', 'sgst_rate', 'igst_rate', 'cess_rate',
                'sale_discount', 'low_level_limit', 'product_type', 'location_rack',
                'serial_no', 'product_description',
                'print_description', 'print_serial_no', 'one_click_sale',
                'not_for_sale', 'enable_tracking'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
