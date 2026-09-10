<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add item_description to purchase_items table
        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'item_description')) {
                $table->text('item_description')->nullable()->after('product_name');
            }
        });

        // Make purchase_price and mrp nullable in products table
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->nullable()->default(0.00)->change();
            $table->decimal('mrp', 10, 2)->nullable()->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert purchase_price and mrp to not nullable
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->nullable(false)->default(0.00)->change();
            $table->decimal('mrp', 10, 2)->nullable(false)->default(0.00)->change();
        });

        // Remove item_description from purchase_items table
        Schema::table('purchase_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_items', 'item_description')) {
                $table->dropColumn('item_description');
            }
        });
    }
};
