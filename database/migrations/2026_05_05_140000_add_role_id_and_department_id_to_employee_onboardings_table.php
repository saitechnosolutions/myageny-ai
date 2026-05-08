<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_onboardings', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_onboardings', 'role_id')) {
                $table->foreignId('role_id')
                    ->nullable()
                    ->after('employee_id')
                    ->constrained('roles')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('employee_onboardings', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->after('role_id')
                    ->constrained('departments')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_onboardings', function (Blueprint $table) {
            if (Schema::hasColumn('employee_onboardings', 'department_id')) {
                $table->dropConstrainedForeignId('department_id');
            }

            if (Schema::hasColumn('employee_onboardings', 'role_id')) {
                $table->dropConstrainedForeignId('role_id');
            }
        });
    }
};
