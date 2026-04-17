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
        Schema::table('lead_products', function (Blueprint $table) {
             $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('restrict');
                $table->string('deal_name')->index();
             $table->text('remarks')->nullable();



            // Payment summary (denormalized for fast reads — updated by trigger/observer)
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_products', function (Blueprint $table) {
            //
        });
    }
};
