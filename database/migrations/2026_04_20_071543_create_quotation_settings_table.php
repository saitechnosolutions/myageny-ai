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
        Schema::create('quotation_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('key', 100);      // e.g. 'logo', 'theme_color', 'prefix', 'terms', 'signature', 'address'
            $table->longText('value')->nullable();
            $table->enum('type', ['text', 'color', 'file', 'html', 'number', 'boolean'])
                  ->default('text');
            $table->string('label', 150)->nullable();
            $table->string('group', 50)->nullable();  // branding, numbering, content, appearance
            $table->string('description')->nullable();
            $table->timestamps();

            // One setting per key per branch (null branch = global default)
            $table->unique(['branch_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_settings');
    }
};
