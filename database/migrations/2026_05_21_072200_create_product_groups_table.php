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
        if (!Schema::hasTable('product_groups')) {
            Schema::create('product_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });

            // Seed default groups
            $defaults = [
                'Medicines', 'Medical Devices', 'Surgical', 
                'OTC / General', 'Nutraceuticals', 'Ayurvedic', 
                'Cosmetics', 'Diagnostics'
            ];
            foreach ($defaults as $name) {
                \App\Models\ProductGroup::create(['name' => $name]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_groups');
    }
};
