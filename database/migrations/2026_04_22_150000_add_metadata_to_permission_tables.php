<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'display_name')) {
                $table->string('display_name')->nullable()->after('name');
            }

            if (! Schema::hasColumn('roles', 'description')) {
                $table->string('description', 500)->nullable()->after('display_name');
            }
        });

        Schema::table('permissions', function (Blueprint $table) {
            if (! Schema::hasColumn('permissions', 'display_name')) {
                $table->string('display_name')->nullable()->after('name');
            }

            if (! Schema::hasColumn('permissions', 'module')) {
                $table->string('module', 50)->nullable()->after('display_name');
            }

            if (! Schema::hasColumn('permissions', 'description')) {
                $table->string('description', 500)->nullable()->after('module');
            }
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('roles', 'display_name')) {
                $dropColumns[] = 'display_name';
            }

            if (Schema::hasColumn('roles', 'description')) {
                $dropColumns[] = 'description';
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });

        Schema::table('permissions', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('permissions', 'display_name')) {
                $dropColumns[] = 'display_name';
            }

            if (Schema::hasColumn('permissions', 'module')) {
                $dropColumns[] = 'module';
            }

            if (Schema::hasColumn('permissions', 'description')) {
                $dropColumns[] = 'description';
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
