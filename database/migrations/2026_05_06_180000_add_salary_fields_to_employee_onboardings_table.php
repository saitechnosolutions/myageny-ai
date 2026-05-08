<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_onboardings', function (Blueprint $table) {
            $table->date('salary_effective_from')->nullable()->after('bank_branch');
            $table->decimal('gross_salary', 12, 2)->nullable()->after('salary_effective_from');
            $table->decimal('basic_salary', 12, 2)->nullable()->after('gross_salary');
            $table->decimal('hra', 12, 2)->nullable()->after('basic_salary');
            $table->decimal('special_allowance', 12, 2)->nullable()->after('hra');
            $table->decimal('other_allowance', 12, 2)->nullable()->after('special_allowance');
            $table->boolean('esi_enabled')->default(false)->after('other_allowance');
            $table->string('esi_no')->nullable()->after('esi_enabled');
            $table->decimal('esi_employee_contribution', 12, 2)->nullable()->after('esi_no');
            $table->decimal('esi_employer_contribution', 12, 2)->nullable()->after('esi_employee_contribution');
            $table->boolean('pf_enabled')->default(false)->after('esi_employer_contribution');
            $table->string('uan_no')->nullable()->after('pf_enabled');
            $table->string('pf_account_no')->nullable()->after('uan_no');
            $table->decimal('pf_employee_contribution', 12, 2)->nullable()->after('pf_account_no');
            $table->decimal('pf_employer_contribution', 12, 2)->nullable()->after('pf_employee_contribution');
            $table->decimal('professional_tax', 12, 2)->nullable()->after('pf_employer_contribution');
            $table->decimal('tds_amount', 12, 2)->nullable()->after('professional_tax');
            $table->decimal('loan_deduction', 12, 2)->nullable()->after('tds_amount');
            $table->decimal('other_deduction', 12, 2)->nullable()->after('loan_deduction');
            $table->decimal('total_deduction', 12, 2)->nullable()->after('other_deduction');
            $table->decimal('net_salary', 12, 2)->nullable()->after('total_deduction');
            $table->string('salary_payment_mode', 30)->nullable()->after('net_salary');
            $table->text('deduction_notes')->nullable()->after('salary_payment_mode');
        });
    }

    public function down(): void
    {
        Schema::table('employee_onboardings', function (Blueprint $table) {
            $table->dropColumn([
                'salary_effective_from',
                'gross_salary',
                'basic_salary',
                'hra',
                'special_allowance',
                'other_allowance',
                'esi_enabled',
                'esi_no',
                'esi_employee_contribution',
                'esi_employer_contribution',
                'pf_enabled',
                'uan_no',
                'pf_account_no',
                'pf_employee_contribution',
                'pf_employer_contribution',
                'professional_tax',
                'tds_amount',
                'loan_deduction',
                'other_deduction',
                'total_deduction',
                'net_salary',
                'salary_payment_mode',
                'deduction_notes',
            ]);
        });
    }
};
