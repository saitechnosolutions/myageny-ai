<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('leads', function (Blueprint $table) {
        $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete()->after('lead_status');
    });
}

public function down(): void
{
    Schema::table('leads', function (Blueprint $table) {
        $table->dropForeignIdFor(\App\Models\Product::class);
        $table->dropColumn('product_id');
    });
}
};
