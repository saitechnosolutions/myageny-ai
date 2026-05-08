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
        Schema::table('quotations', function (Blueprint $table) {
            if (! Schema::hasColumn('quotations', 'customer_state')) {
                $table->string('customer_state', 100)->nullable()->after('gst_number');
            }

            if (! Schema::hasColumn('quotations', 'seller_state')) {
                $table->string('seller_state', 100)->nullable()->after('customer_state');
            }

            if (! Schema::hasColumn('quotations', 'tax_type')) {
                $table->string('tax_type', 20)->nullable()->after('seller_state');
            }

            if (! Schema::hasColumn('quotations', 'cgst_rate')) {
                $table->decimal('cgst_rate', 5, 2)->default(0)->after('tax');
            }

            if (! Schema::hasColumn('quotations', 'sgst_rate')) {
                $table->decimal('sgst_rate', 5, 2)->default(0)->after('cgst_rate');
            }

            if (! Schema::hasColumn('quotations', 'igst_rate')) {
                $table->decimal('igst_rate', 5, 2)->default(0)->after('sgst_rate');
            }

            if (! Schema::hasColumn('quotations', 'cgst_amount')) {
                $table->decimal('cgst_amount', 12, 2)->default(0)->after('tax_amount');
            }

            if (! Schema::hasColumn('quotations', 'sgst_amount')) {
                $table->decimal('sgst_amount', 12, 2)->default(0)->after('cgst_amount');
            }

            if (! Schema::hasColumn('quotations', 'igst_amount')) {
                $table->decimal('igst_amount', 12, 2)->default(0)->after('sgst_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            foreach ([
                'customer_state',
                'seller_state',
                'tax_type',
                'cgst_rate',
                'sgst_rate',
                'igst_rate',
                'cgst_amount',
                'sgst_amount',
                'igst_amount',
            ] as $column) {
                if (Schema::hasColumn('quotations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
