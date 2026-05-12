<?php

namespace App\Http\Controllers\HRMS;

use App\Http\Controllers\Controller;
use App\Models\DailyAttendance;
use App\Models\Department;
use App\Models\EmployeeOnboarding;
use App\Models\HolidayCalendar;
use App\Models\InternJoiningForm;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Basic counts
        $employees_total = EmployeeOnboarding::count();
        $employees_pending = EmployeeOnboarding::where('status', 'pending')->count();
        $employees_verified = EmployeeOnboarding::where('status', 'verified')->count();
        $interns_total = InternJoiningForm::count();

        // Today's attendance stats
        $today_attendance = DailyAttendance::where('attendance_date', $today)->get();
        $today_present = $today_attendance->where('attendance_status', 'present')->count();
        $today_late = $today_attendance->where('attendance_status', 'late')->count();
        $today_absent = $employees_total - $today_present - $today_late;

        // Department-wise employee count and salary
        $department_stats = Department::select('departments.*')
            ->selectRaw('COUNT(eo.id) as employee_count')
            ->selectRaw('COALESCE(SUM(eo.gross_salary), 0) as total_salary')
            ->leftJoin('employee_onboardings as eo', function($join) {
                $join->on('eo.department_id', '=', 'departments.id')
                     ->where('eo.status', 'verified');
            })
            ->where('departments.deleted_at', null)
            ->groupBy('departments.id', 'departments.name', 'departments.description', 'departments.created_at', 'departments.updated_at', 'departments.deleted_at')
            ->get();

        // Today's birthdays
        $today_birthdays = EmployeeOnboarding::whereMonth('date_of_birth', $today->month)
            ->whereDay('date_of_birth', $today->day)
            ->where('status', 'verified')
            ->with('role')
            ->get();

        // Upcoming holidays (next 7 days)
        $upcoming_holidays = HolidayCalendar::where('holiday_date', '>=', $today)
            ->where('holiday_date', '<=', $today->copy()->addDays(7))
            ->orderBy('holiday_date')
            ->get();

        // Payroll information
        $salary_day_1_employees = EmployeeOnboarding::where('status', 'verified')
            ->where('salary_payment_mode', 'monthly_1st')
            ->count();

        $salary_day_10_employees = EmployeeOnboarding::where('status', 'verified')
            ->where('salary_payment_mode', 'monthly_10th')
            ->count();

        // Monthly leave data (last 6 months)
        $monthly_leave_data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $leaves = DailyAttendance::whereYear('attendance_date', $month->year)
                ->whereMonth('attendance_date', $month->month)
                ->where('attendance_status', 'leave')
                ->count();

            $monthly_leave_data[] = [
                'month' => $month->format('M Y'),
                'leaves' => $leaves
            ];
        }

        // Sample announcements
        $announcements = [
            [
                'title' => 'New HR Policy Update',
                'message' => 'Please review the updated leave policy effective from next month.',
                'priority' => 'high',
                'date' => $today->format('Y-m-d')
            ],
            [
                'title' => 'Team Building Event',
                'message' => 'Join us for the quarterly team building event on Friday.',
                'priority' => 'medium',
                'date' => $today->copy()->addDays(2)->format('Y-m-d')
            ]
        ];

        $stats = [
            'employees_total' => $employees_total,
            'employees_pending' => $employees_pending,
            'employees_verified' => $employees_verified,
            'interns_total' => $interns_total,
            'today_present' => $today_present,
            'today_late' => $today_late,
            'today_absent' => $today_absent,
            'department_stats' => $department_stats,
            'today_birthdays' => $today_birthdays,
            'upcoming_holidays' => $upcoming_holidays,
            'salary_day_1_employees' => $salary_day_1_employees,
            'salary_day_10_employees' => $salary_day_10_employees,
            'monthly_leave_data' => $monthly_leave_data,
            'announcements' => $announcements,
        ];

        return view('pages.hrms.dashboard.index', compact('stats'));
    }
}