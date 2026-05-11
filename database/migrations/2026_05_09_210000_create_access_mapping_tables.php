<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->enum('access_level', ['company', 'team', 'self'])->default('self');
            $table->timestamps();

            $table->unique('role_id');
            $table->index(['company_id', 'access_level']);
        });

        Schema::create('user_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['manager_id', 'user_id']);
            $table->unique('user_id');
            $table->index(['company_id', 'manager_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_mappings');
        Schema::dropIfExists('role_mappings');
    }
};
