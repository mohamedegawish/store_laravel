<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)->nullable()->after('price');
            $table->decimal('discount', 10, 2)->nullable()->after('cost'); // Discount amount or percentage (we can treat it as fixed amount for simplicity, or 0-100 if % based. Let's stick to fixed amount)
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['cost', 'discount']);
        });
    }
};
