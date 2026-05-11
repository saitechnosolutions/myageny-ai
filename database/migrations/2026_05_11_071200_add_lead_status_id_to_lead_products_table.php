<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_products', function (Blueprint $table) {
            if (! Schema::hasColumn('lead_products', 'lead_status_id')) {
                $table->foreignId('lead_status_id')
                    ->nullable()
                    ->after('product_status')
                    ->constrained('lead_statuses')
                    ->nullOnDelete();
            }
        });

        if (! Schema::hasColumn('lead_products', 'lead_status_id')) {
            return;
        }

        $statuses = DB::table('lead_statuses')
            ->select('id', 'name', 'company_id')
            ->get()
            ->groupBy('company_id')
            ->map(function ($rows) {
                return $rows->keyBy(fn ($row) => $this->normalizeStatusName($row->name));
            });

        DB::table('lead_products')
            ->select('id', 'company_id', 'product_status')
            ->whereNull('lead_status_id')
            ->orderBy('id')
            ->chunkById(500, function ($products) use ($statuses) {
                foreach ($products as $product) {
                    $statusKey = $this->normalizeStatusName($product->product_status);
                    $companyStatuses = $statuses->get($product->company_id);
                    $globalStatuses = $statuses->get('');
                    $matchedStatus = $companyStatuses?->get($statusKey)
                        ?? $globalStatuses?->get($statusKey);

                    if (! $matchedStatus) {
                        continue;
                    }

                    DB::table('lead_products')
                        ->where('id', $product->id)
                        ->update(['lead_status_id' => $matchedStatus->id]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('lead_products', function (Blueprint $table) {
            if (Schema::hasColumn('lead_products', 'lead_status_id')) {
                $table->dropConstrainedForeignId('lead_status_id');
            }
        });
    }

    private function normalizeStatusName(?string $name): string
    {
        return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '_', (string) $name), '_'));
    }
};
