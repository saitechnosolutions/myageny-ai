<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'super_admin_user_id')) {
                $table->foreignId('super_admin_user_id')->nullable()->after('facebook_client_secret')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
                $table->index('company_id');
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
                $table->index('company_id');
            }
        });

        Schema::table('permissions', function (Blueprint $table) {
            if (! Schema::hasColumn('permissions', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
                $table->index('company_id');
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
                $table->index('company_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
                $table->index('company_id');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (! Schema::hasColumn('quotations', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
                $table->index('company_id');
            }
        });

        Schema::table('lead_call_updates', function (Blueprint $table) {
            if (! Schema::hasColumn('lead_call_updates', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
                $table->index('company_id');
            }
        });

        Schema::table('lead_products', function (Blueprint $table) {
            if (! Schema::hasColumn('lead_products', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
                $table->index('company_id');
            }
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            if (! Schema::hasColumn('quotation_items', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
                $table->index('company_id');
            }
        });

        DB::table('users')
            ->whereNull('company_id')
            ->whereNotNull('email')
            ->update([
                'company_id' => DB::raw("(SELECT companies.id FROM companies WHERE companies.email = users.email LIMIT 1)"),
            ]);

        DB::statement("
            UPDATE companies
            INNER JOIN users ON users.email = companies.email
            SET users.company_id = companies.id
            WHERE users.company_id IS NULL
        ");

        DB::statement("
            UPDATE leads
            LEFT JOIN users ON users.id = leads.created_by
            SET leads.company_id = users.company_id
            WHERE leads.company_id IS NULL
        ");

        if (Schema::hasColumn('products', 'created_by')) {
            DB::statement("
                UPDATE products
                LEFT JOIN users ON users.id = products.created_by
                SET products.company_id = users.company_id
                WHERE products.company_id IS NULL
            ");
        }

        if (Schema::hasColumn('quotations', 'created_by')) {
            DB::statement("
                UPDATE quotations
                LEFT JOIN leads ON leads.id = quotations.lead_id
                LEFT JOIN users ON users.id = quotations.created_by
                SET quotations.company_id = COALESCE(leads.company_id, users.company_id)
                WHERE quotations.company_id IS NULL
            ");
        } else {
            DB::statement("
                UPDATE quotations
                LEFT JOIN leads ON leads.id = quotations.lead_id
                SET quotations.company_id = leads.company_id
                WHERE quotations.company_id IS NULL
            ");
        }

        DB::statement("
            UPDATE lead_call_updates
            INNER JOIN leads ON leads.id = lead_call_updates.lead_id
            SET lead_call_updates.company_id = leads.company_id
            WHERE lead_call_updates.company_id IS NULL
        ");

        DB::statement("
            UPDATE lead_products
            INNER JOIN leads ON leads.id = lead_products.lead_id
            SET lead_products.company_id = leads.company_id
            WHERE lead_products.company_id IS NULL
        ");

        DB::statement("
            UPDATE quotation_items
            INNER JOIN quotations ON quotations.id = quotation_items.quotation_id
            SET quotation_items.company_id = quotations.company_id
            WHERE quotation_items.company_id IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            if (Schema::hasColumn('quotation_items', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });

        Schema::table('lead_products', function (Blueprint $table) {
            if (Schema::hasColumn('lead_products', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });

        Schema::table('lead_call_updates', function (Blueprint $table) {
            if (Schema::hasColumn('lead_call_updates', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });

        Schema::table('permissions', function (Blueprint $table) {
            if (Schema::hasColumn('permissions', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });

        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'super_admin_user_id')) {
                $table->dropConstrainedForeignId('super_admin_user_id');
            }
        });
    }
};
