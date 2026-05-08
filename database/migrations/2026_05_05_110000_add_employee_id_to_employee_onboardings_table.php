<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_onboardings', function (Blueprint $table) {
            $table->string('employee_id', 50)->nullable()->after('id')->unique();
        });
    }

    public function down(): void
    {
        Schema::table('employee_onboardings', function (Blueprint $table) {
            $table->dropUnique(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
};
