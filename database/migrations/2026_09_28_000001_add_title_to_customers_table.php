<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Name prefix (Mr, Mrs, ...). Production already has this column from an
     * earlier server-only migration, hence the guard.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('customers', 'title')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('title', 10)->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'title')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
    }
};
