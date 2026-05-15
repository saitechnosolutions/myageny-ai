<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_hierarchy_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('parent_role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('child_role_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();

            $table->unique('child_role_id');
            $table->index(['company_id', 'parent_role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_hierarchy_mappings');
    }
};
