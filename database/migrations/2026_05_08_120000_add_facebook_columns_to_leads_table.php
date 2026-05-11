<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'facebook_lead_id')) {
                $table->string('facebook_lead_id')->nullable()->after('created_by');
                $table->index('facebook_lead_id');
            }

            if (!Schema::hasColumn('leads', 'facebook_campaign_id')) {
                $table->string('facebook_campaign_id')->nullable()->after('facebook_lead_id');
                $table->index('facebook_campaign_id');
            }

            if (!Schema::hasColumn('leads', 'facebook_payload')) {
                $table->json('facebook_payload')->nullable()->after('facebook_campaign_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'facebook_payload')) {
                $table->dropColumn('facebook_payload');
            }

            if (Schema::hasColumn('leads', 'facebook_campaign_id')) {
                $table->dropIndex(['facebook_campaign_id']);
                $table->dropColumn('facebook_campaign_id');
            }

            if (Schema::hasColumn('leads', 'facebook_lead_id')) {
                $table->dropIndex(['facebook_lead_id']);
                $table->dropColumn('facebook_lead_id');
            }
        });
    }
};