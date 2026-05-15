<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('customer_response', 20)->default('pending')->after('is_approved')->index();
            $table->timestamp('customer_responded_at')->nullable()->after('customer_response');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['customer_response', 'customer_responded_at']);
        });
    }
};
