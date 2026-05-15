<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_onboardings', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_onboardings', 'portal_user_id')) {
                $table->foreignId('portal_user_id')
                    ->nullable()
                    ->after('department_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_onboardings', function (Blueprint $table) {
            if (Schema::hasColumn('employee_onboardings', 'portal_user_id')) {
                $table->dropConstrainedForeignId('portal_user_id');
            }
        });
    }
};
