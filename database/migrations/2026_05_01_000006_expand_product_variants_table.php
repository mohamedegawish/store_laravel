<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('product_variants', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('product_variants', 'offer_price')) {
                $table->decimal('offer_price', 10, 2)->nullable()->after('discount');
            }
            if (!Schema::hasColumn('product_variants', 'offer_starts_at')) {
                $table->timestamp('offer_starts_at')->nullable()->after('offer_price');
            }
            if (!Schema::hasColumn('product_variants', 'offer_ends_at')) {
                $table->timestamp('offer_ends_at')->nullable()->after('offer_starts_at');
            }
            if (!Schema::hasColumn('product_variants', 'min_stock_alert')) {
                $table->unsignedInteger('min_stock_alert')->default(5)->after('stock_quantity');
            }
            if (!Schema::hasColumn('product_variants', 'weight')) {
                $table->decimal('weight', 8, 3)->nullable()->after('min_stock_alert');
            }
            if (!Schema::hasColumn('product_variants', 'image')) {
                $table->string('image')->nullable()->after('weight');
            }
            if (!Schema::hasColumn('product_variants', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('image');
            }
            if (!Schema::hasColumn('product_variants', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_default');
            }
            if (!Schema::hasColumn('product_variants', 'sort_order')) {
                $table->unsignedSmallInteger('sort_order')->default(0)->after('is_active');
            }
        });

        // Pivot: variant <-> attribute_value
        if (!Schema::hasTable('product_variant_attributes')) {
            Schema::create('product_variant_attributes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
                $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
                $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['variant_id', 'attribute_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_attributes');
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar', 'name_en', 'offer_price', 'offer_starts_at', 'offer_ends_at',
                'min_stock_alert', 'weight', 'image', 'is_default', 'is_active', 'sort_order',
            ]);
        });
    }
};
