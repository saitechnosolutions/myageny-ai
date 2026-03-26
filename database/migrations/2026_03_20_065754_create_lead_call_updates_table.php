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
        Schema::create('lead_call_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('called_at');
            $table->string('call_type')->default('outgoing'); // outgoing, incoming, missed
            $table->integer('duration_minutes')->nullable();   // call duration
            $table->string('outcome');                         // interested, not_interested, callback, no_answer, follow_up, closed
            $table->text('notes')->nullable();
            $table->date('next_follow_up')->nullable();
            $table->timestamps();

            $table->index('lead_id');
            $table->index('called_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_call_updates');
    }
};
