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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']); // credit = money in, debit = money out
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('reference_number')->nullable();
            $table->string('category')->nullable(); // e.g., "Invoice Payment", "Expense", "Transfer"
            $table->text('description')->nullable();
            $table->string('payment_method')->nullable(); // cash, card, upi, cheque
            $table->foreignId('related_invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->foreignId('related_expense_id')->nullable()->constrained('expenses')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
