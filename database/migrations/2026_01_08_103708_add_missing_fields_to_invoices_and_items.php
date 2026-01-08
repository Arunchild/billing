<?php

use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add fields to invoices table
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'place_of_supply')) {
                $table->string('place_of_supply')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'credit_period')) {
                $table->integer('credit_period')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'bill_to_type')) {
                $table->enum('bill_to_type', ['cash', 'customer'])->default('customer');
            }
            if (!Schema::hasColumn('invoices', 'contact_no')) {
                $table->string('contact_no')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'customer_address')) {
                $table->text('customer_address')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'customer_gstin')) {
                $table->string('customer_gstin')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'sold_by')) {
                $table->string('sold_by')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'delivery_terms')) {
                $table->text('delivery_terms')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'remarks')) {
                $table->text('remarks')->nullable();
            }
            
            if (!Schema::hasColumn('invoices', 'payment_1_date')) {
                $table->date('payment_1_date')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'payment_1_mode')) {
                $table->string('payment_1_mode')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'payment_1_txn_id')) {
                $table->string('payment_1_txn_id')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'payment_1_amount')) {
                $table->decimal('payment_1_amount', 15, 2)->default(0);
            }
            
            if (!Schema::hasColumn('invoices', 'payment_2_date')) {
                $table->date('payment_2_date')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'payment_2_mode')) {
                $table->string('payment_2_mode')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'payment_2_txn_id')) {
                $table->string('payment_2_txn_id')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'payment_2_amount')) {
                $table->decimal('payment_2_amount', 15, 2)->default(0);
            }
            
            if (!Schema::hasColumn('invoices', 'shipping_charges')) {
                $table->decimal('shipping_charges', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'reference_number')) {
                $table->string('reference_number')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'balance_amount')) {
                $table->decimal('balance_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'cess_total')) {
                $table->decimal('cess_total', 10, 2)->default(0);
            }
        });

        // Add fields to invoice_items table
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'serial_no')) {
                $table->integer('serial_no')->nullable();
            }
            if (!Schema::hasColumn('invoice_items', 'unit')) {
                $table->string('unit')->nullable();
            }
            if (!Schema::hasColumn('invoice_items', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('invoice_items', 'mrp')) {
                $table->decimal('mrp', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('invoice_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('invoice_items', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('invoice_items', 'cess_percentage')) {
                $table->decimal('cess_percentage', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('invoice_items', 'item_tag')) {
                $table->string('item_tag')->nullable();
            }
            if (!Schema::hasColumn('invoice_items', 'item_code')) {
                $table->string('item_code')->nullable();
            }
            if (!Schema::hasColumn('invoice_items', 'item_description')) {
                $table->text('item_description')->nullable();
            }
            if (!Schema::hasColumn('invoice_items', 'net_price')) {
                $table->decimal('net_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('invoice_items', 'amount_before_tax')) {
                $table->decimal('amount_before_tax', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('invoice_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('invoice_items', 'cess_amount')) {
                $table->decimal('cess_amount', 10, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $columns = ['place_of_supply', 'credit_period', 'bill_to_type', 'contact_no',
                'customer_name', 'customer_address', 'customer_gstin', 'sold_by',
                'delivery_terms', 'remarks', 'payment_1_date', 'payment_1_mode',
                'payment_1_txn_id', 'payment_1_amount', 'payment_2_date', 'payment_2_mode',
                'payment_2_txn_id', 'payment_2_amount', 'shipping_charges', 'reference_number',
                'balance_amount', 'cess_total'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $columns = ['serial_no', 'unit', 'sale_price', 'mrp', 'discount_percentage',
                'tax_percentage', 'cess_percentage', 'item_tag', 'item_code',
                'item_description', 'net_price', 'amount_before_tax', 'discount_amount',
                'cess_amount'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoice_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
