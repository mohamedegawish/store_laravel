<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            
            // Branding
            $table->string('primary_color')->default('#6C3FC5');
            $table->string('secondary_color')->default('#EFE9FA');
            $table->string('font_family')->default('Ping-Medium');
            $table->enum('theme_mode', ['light', 'dark', 'system'])->default('light');
            
            // Layout config
            $table->json('homepage_layout')->nullable(); // Config for section ordering
            $table->json('banners')->nullable(); // Sliders/hero banners
            
            // Toggles
            $table->boolean('show_low_stock_badges')->default(true);
            $table->boolean('show_trending_badges')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
