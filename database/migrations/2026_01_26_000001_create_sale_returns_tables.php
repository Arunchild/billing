<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->date('return_date');
            
            // Link to original invoice (optional)
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            
            $table->decimal('sub_total', 15, 2);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->enum('status', ['pending', 'approved', 'refunded'])->default('pending');
            $table->enum('type', ['gst', 'non_gst'])->default('non_gst');
            
            // Additional fields matching invoices
            $table->string('place_of_supply')->nullable();
            $table->integer('credit_period')->nullable();
            $table->enum('bill_to_type', ['cash', 'customer'])->default('customer');
            $table->string('contact_no')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_gstin')->nullable();
            $table->string('sold_by')->nullable();
            $table->text('delivery_terms')->nullable();
            $table->text('remarks')->nullable();
            $table->decimal('shipping_charges', 10, 2)->default(0);
            $table->string('reference_number')->nullable();
            $table->decimal('balance_amount', 15, 2)->default(0);
            $table->decimal('cess_total', 10, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sale_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_return_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            
            // Additional fields
            $table->integer('serial_no')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('cess_percentage', 5, 2)->default(0);
            $table->string('item_tag')->nullable();
            $table->string('item_code')->nullable();
            $table->text('item_description')->nullable();
            $table->decimal('net_price', 10, 2)->nullable();
            $table->decimal('amount_before_tax', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('cess_amount', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_return_items');
        Schema::dropIfExists('sale_returns');
    }
};
