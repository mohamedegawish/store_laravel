<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_name_ar')) {
                $table->string('product_name_ar')->nullable()->after('product_variant_id');
            }
            if (!Schema::hasColumn('order_items', 'product_name_en')) {
                $table->string('product_name_en')->nullable()->after('product_name_ar');
            }
            if (!Schema::hasColumn('order_items', 'variant_name')) {
                $table->string('variant_name')->nullable()->after('product_name_en');
            }
            if (!Schema::hasColumn('order_items', 'sku')) {
                $table->string('sku')->nullable()->after('variant_name');
            }
            if (!Schema::hasColumn('order_items', 'product_image')) {
                $table->string('product_image')->nullable()->after('sku');
            }
            if (!Schema::hasColumn('order_items', 'product_snapshot')) {
                $table->json('product_snapshot')->nullable()->after('product_image');
            }
            if (!Schema::hasColumn('order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('unit_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'product_name_ar', 'product_name_en', 'variant_name', 'sku',
                'product_image', 'product_snapshot', 'discount_amount',
            ]);
        });
    }
};
