<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            // Store identity
            if (!Schema::hasColumn('store_settings', 'store_name_ar')) {
                $table->string('store_name_ar')->nullable()->after('company_id');
            }
            if (!Schema::hasColumn('store_settings', 'store_name_en')) {
                $table->string('store_name_en')->nullable()->after('store_name_ar');
            }
            if (!Schema::hasColumn('store_settings', 'logo')) {
                $table->string('logo')->nullable()->after('store_name_en');
            }
            if (!Schema::hasColumn('store_settings', 'favicon')) {
                $table->string('favicon')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('store_settings', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('favicon');
            }
            // Colors - accent already handled by primary/secondary
            if (!Schema::hasColumn('store_settings', 'accent_color')) {
                $table->string('accent_color')->default('#10B981')->after('secondary_color');
            }
            // Contact info
            if (!Schema::hasColumn('store_settings', 'phone')) {
                $table->string('phone')->nullable()->after('accent_color');
            }
            if (!Schema::hasColumn('store_settings', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('store_settings', 'address_ar')) {
                $table->string('address_ar')->nullable()->after('email');
            }
            if (!Schema::hasColumn('store_settings', 'address_en')) {
                $table->string('address_en')->nullable()->after('address_ar');
            }
            // Social links
            if (!Schema::hasColumn('store_settings', 'social_links')) {
                $table->json('social_links')->nullable()->after('address_en');
            }
            // SEO
            if (!Schema::hasColumn('store_settings', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('social_links');
            }
            if (!Schema::hasColumn('store_settings', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            // Footer
            if (!Schema::hasColumn('store_settings', 'footer_text_ar')) {
                $table->text('footer_text_ar')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('store_settings', 'footer_text_en')) {
                $table->text('footer_text_en')->nullable()->after('footer_text_ar');
            }
            // Hero / Homepage
            if (!Schema::hasColumn('store_settings', 'hero_title_ar')) {
                $table->string('hero_title_ar')->nullable()->after('footer_text_en');
            }
            if (!Schema::hasColumn('store_settings', 'hero_title_en')) {
                $table->string('hero_title_en')->nullable()->after('hero_title_ar');
            }
            if (!Schema::hasColumn('store_settings', 'hero_subtitle_ar')) {
                $table->text('hero_subtitle_ar')->nullable()->after('hero_title_en');
            }
            if (!Schema::hasColumn('store_settings', 'hero_subtitle_en')) {
                $table->text('hero_subtitle_en')->nullable()->after('hero_subtitle_ar');
            }
            // Commerce settings
            if (!Schema::hasColumn('store_settings', 'currency')) {
                $table->string('currency', 10)->default('SAR')->after('hero_subtitle_en');
            }
            if (!Schema::hasColumn('store_settings', 'currency_symbol')) {
                $table->string('currency_symbol', 10)->default('ر.س')->after('currency');
            }
            if (!Schema::hasColumn('store_settings', 'shipping_cost')) {
                $table->decimal('shipping_cost', 10, 2)->default(0)->after('currency_symbol');
            }
            if (!Schema::hasColumn('store_settings', 'free_shipping_threshold')) {
                $table->decimal('free_shipping_threshold', 10, 2)->nullable()->after('shipping_cost');
            }
            if (!Schema::hasColumn('store_settings', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0)->after('free_shipping_threshold');
            }
            if (!Schema::hasColumn('store_settings', 'min_order_amount')) {
                $table->decimal('min_order_amount', 10, 2)->default(0)->after('tax_rate');
            }
            // Feature toggles
            if (!Schema::hasColumn('store_settings', 'enable_reviews')) {
                $table->boolean('enable_reviews')->default(true)->after('min_order_amount');
            }
            if (!Schema::hasColumn('store_settings', 'enable_wishlist')) {
                $table->boolean('enable_wishlist')->default(true)->after('enable_reviews');
            }
            if (!Schema::hasColumn('store_settings', 'enable_newsletter')) {
                $table->boolean('enable_newsletter')->default(true)->after('enable_wishlist');
            }
            if (!Schema::hasColumn('store_settings', 'maintenance_mode')) {
                $table->boolean('maintenance_mode')->default(false)->after('enable_newsletter');
            }
            if (!Schema::hasColumn('store_settings', 'default_locale')) {
                $table->string('default_locale', 5)->default('ar')->after('maintenance_mode');
            }
            // Featured sections config
            if (!Schema::hasColumn('store_settings', 'sections_config')) {
                $table->json('sections_config')->nullable()->after('default_locale');
            }
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'store_name_ar', 'store_name_en', 'logo', 'favicon', 'cover_image',
                'accent_color', 'phone', 'email', 'address_ar', 'address_en', 'social_links',
                'meta_title', 'meta_description', 'footer_text_ar', 'footer_text_en',
                'hero_title_ar', 'hero_title_en', 'hero_subtitle_ar', 'hero_subtitle_en',
                'currency', 'currency_symbol', 'shipping_cost', 'free_shipping_threshold',
                'tax_rate', 'min_order_amount', 'enable_reviews', 'enable_wishlist',
                'enable_newsletter', 'maintenance_mode', 'default_locale', 'sections_config',
            ]);
        });
    }
};
