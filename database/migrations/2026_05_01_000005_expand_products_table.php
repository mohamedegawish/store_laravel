<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Bilingual names
            if (!Schema::hasColumn('products', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('products', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_ar');
            }
            // Slug
            if (!Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->after('name_en');
            }
            // Short descriptions
            if (!Schema::hasColumn('products', 'short_description_ar')) {
                $table->text('short_description_ar')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('products', 'short_description_en')) {
                $table->text('short_description_en')->nullable()->after('short_description_ar');
            }
            // Bilingual full descriptions
            if (!Schema::hasColumn('products', 'description_ar')) {
                $table->longText('description_ar')->nullable()->after('short_description_en');
            }
            if (!Schema::hasColumn('products', 'description_en')) {
                $table->longText('description_en')->nullable()->after('description_ar');
            }
            // Brand
            if (!Schema::hasColumn('products', 'brand_id')) {
                $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete()->after('category_id');
            }
            // SKU / Barcode
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('description_en');
            }
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('sku');
            }
            // Pricing type
            if (!Schema::hasColumn('products', 'pricing_type')) {
                $table->enum('pricing_type', ['per_piece', 'per_kg', 'per_gram', 'per_liter', 'per_package'])->default('per_piece')->after('barcode');
            }
            if (!Schema::hasColumn('products', 'unit_label')) {
                $table->string('unit_label')->nullable()->after('pricing_type');
            }
            // Weight & dimensions
            if (!Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 8, 3)->nullable()->after('unit_label');
            }
            // Tax
            if (!Schema::hasColumn('products', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0)->after('weight');
            }
            // Flags
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('products', 'is_trending')) {
                $table->boolean('is_trending')->default(false)->after('is_featured');
            }
            if (!Schema::hasColumn('products', 'is_best_seller')) {
                $table->boolean('is_best_seller')->default(false)->after('is_trending');
            }
            // Status
            if (!Schema::hasColumn('products', 'status')) {
                $table->enum('status', ['active', 'inactive', 'draft'])->default('active')->after('is_best_seller');
            }
            // Sort order
            if (!Schema::hasColumn('products', 'sort_order')) {
                $table->unsignedSmallInteger('sort_order')->default(0)->after('status');
            }
            // Views counter
            if (!Schema::hasColumn('products', 'views_count')) {
                $table->unsignedInteger('views_count')->default(0)->after('sort_order');
            }
            // Related products
            if (!Schema::hasColumn('products', 'related_product_ids')) {
                $table->json('related_product_ids')->nullable()->after('views_count');
            }
            // SEO
            if (!Schema::hasColumn('products', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('related_product_ids');
            }
            if (!Schema::hasColumn('products', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            // Soft deletes
            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar', 'name_en', 'slug', 'short_description_ar', 'short_description_en',
                'description_ar', 'description_en', 'brand_id', 'sku', 'barcode', 'pricing_type',
                'unit_label', 'weight', 'tax_rate', 'is_featured', 'is_trending', 'is_best_seller',
                'status', 'sort_order', 'views_count', 'related_product_ids', 'meta_title',
                'meta_description', 'deleted_at',
            ]);
        });
    }
};
