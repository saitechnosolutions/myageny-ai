<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('email')->unique();
            $table->string('mobile_number', 20);
            $table->text('address');
            $table->unsignedInteger('number_of_accounts')->default(1);
            $table->enum('company_status', ['active', 'inactive'])->default('active');
            $table->string('facebook_client_id');
            $table->string('facebook_client_secret');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};