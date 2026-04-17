<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LeadAndLeadProductMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:lead-and-lead-product-migration-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         $leads = DB::connection('mysql2')->table('leads')->get();
        
            foreach($leads as $lead)
                {
                            DB::connection('mysql')->table('leads')->insert([
                                'company_name' => $lead->CompanyName,
                                'contact_name' => $lead->ClientName,
                                'lead_date' => Carbon::now()->subYears(5)->addDays(rand(0, 1825))->format('Y-m-d'),
                                'mobile_number' => $lead->MobileNumber,
                                'email' => $lead->EmailID,
                                'lead_source' => 'Social Media',
                                'lead_status' => 'New',
                                'branch_id' => 1,
                                'assigned_to' => 1,
                                'created_by' => 1,
                                'created_at' => $lead->created_at,
                                
                    ]);
                }
    }
}