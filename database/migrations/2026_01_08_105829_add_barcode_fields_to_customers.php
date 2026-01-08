<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'reg_no')) {
                $table->string('reg_no')->unique()->nullable();
            }
            if (!Schema::hasColumn('customers', 'age')) {
                $table->integer('age')->nullable();
            }
            if (!Schema::hasColumn('customers', 'gender')) {
                $table->enum('gender', ['M', 'F', 'Other'])->nullable();
            }
            if (!Schema::hasColumn('customers', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('customers', 'barcode')) {
                $table->string('barcode')->unique()->nullable();
            }
            if (!Schema::hasColumn('customers', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable();
            }
            if (!Schema::hasColumn('customers', 'pincode')) {
                $table->string('pincode', 10)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columns = ['reg_no', 'age', 'gender', 'city', 'barcode', 'date_of_birth', 'pincode'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
