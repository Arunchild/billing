<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fields from the paper registration form that the customer record was missing.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('alternate_phone', 20)->nullable()->after('phone');
            $table->decimal('weight', 5, 1)->nullable()->after('gender');
            $table->decimal('height', 5, 1)->nullable()->after('weight');
            $table->boolean('is_diabetic')->nullable()->after('pincode');
            $table->boolean('on_insulin')->nullable()->after('is_diabetic');
            $table->boolean('latex_allergy')->nullable()->after('on_insulin');
            $table->string('medical_notes', 500)->nullable()->after('latex_allergy');
            $table->string('employment_status', 30)->nullable()->after('medical_notes');
            $table->string('employment_details')->nullable()->after('employment_status');
            $table->string('referral_source', 30)->nullable()->after('employment_details');
            $table->string('referral_details', 500)->nullable()->after('referral_source');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'alternate_phone', 'weight', 'height', 'is_diabetic', 'on_insulin',
                'latex_allergy', 'medical_notes', 'employment_status', 'employment_details',
                'referral_source', 'referral_details',
            ]);
        });
    }
};
