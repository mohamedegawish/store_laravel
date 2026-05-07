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
        Schema::create('companies', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('company_name');
            $table->string('chairman_name');
            $table->string('manager_name');
            $table->string('manager_phone');
            $table->string('product_link');
            $table->string('factory_address');
            $table->string('branches');
            $table->string('exhibitions');
            $table->string('company_email');
            $table->string('website')->nullable();
            $table->string('hotline')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('location_url')->nullable();
            $table->string('company_working_hours')->nullable();
            $table->string('branches_working_hours')->nullable();
            $table->string('exhibitions_working_hours')->nullable();
            $table->string('logo')->nullable();
            $table->foreignId('user_id')->nullable()
                ->constrained()->cascadeOnDelete();




            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
