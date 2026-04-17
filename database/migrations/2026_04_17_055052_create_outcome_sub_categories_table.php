<?php
// database/migrations/2024_01_01_000004_create_outcome_sub_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outcome_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('outcome_categories')
                  ->onDelete('cascade');
            $table->string('name');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();

            $table->index(['company_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outcome_sub_categories');
    }
};