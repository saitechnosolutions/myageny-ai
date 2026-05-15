<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('role_mappings')) {
            return;
        }

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE role_mappings MODIFY access_level ENUM('company', 'team', 'tl', 'self') NOT NULL DEFAULT 'self'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('role_mappings')) {
            return;
        }

        DB::table('role_mappings')
            ->where('access_level', 'tl')
            ->update(['access_level' => 'team']);

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE role_mappings MODIFY access_level ENUM('company', 'team', 'self') NOT NULL DEFAULT 'self'");
        }
    }
};
