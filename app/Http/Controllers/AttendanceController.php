<?php

namespace App\Http\Controllers;

use App\Models\DailyAttendance;
use App\Models\EmployeeOnboarding;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    private const EARLY_LOGIN_BEFORE = '09:00:00';
    private const LATE_LOGIN_AFTER = '09:30:00';

    public function index(Request $request): View
    {
        $validated = $this->validateAttendanceFilters($request);
        $perPage = (int) ($validated['per_page'] ?? 10);
        $attendanceData = $this->buildAttendanceData($validated);

        $attendances = $this->paginateCollection($attendanceData['records'], $perPage, $request->integer('page', 1), $request);

        return view('pages.hrms.attendance.index', [
            'attendances' => $attendances,
            'selectedDate' => Carbon::parse($attendanceData['selected_date']),
            'stats' => $attendanceData['stats'],
            'thresholds' => [
                'early_before' => self::EARLY_LOGIN_BEFORE,
                'late_after' => self::LATE_LOGIN_AFTER,
            ],
        ]);
    }

    public function export(Request $request): Response
    {
        $validated = $this->validateAttendanceFilters($request);
        $attendanceData = $this->buildAttendanceData($validated);
        $selectedDate = Carbon::parse($attendanceData['selected_date']);

        $rows = $attendanceData['records']->map(function (array $record) {
            return [
                'Employee ID' => $record['employee_id'] ?: 'N/A',
                'Employee Name' => $record['employee_name'],
                'Attendance Date' => Carbon::parse($record['attendance_date'])->format('d-m-Y'),
                'Status' => ucfirst($record['attendance_status']),
                'Login Time' => $record['login_time'] ? Carbon::createFromFormat('H:i:s', $record['login_time'])->format('h:i A') : 'N/A',
                'Logout Time' => $record['logout_time'] ? Carbon::createFromFormat('H:i:s', $record['logout_time'])->format('h:i A') : 'N/A',
                'Working Hours' => $record['overall_working_hours'] ?: 'N/A',
                'Login Timing' => $record['login_timing'] ? ucfirst(str_replace('-', ' ', $record['login_timing'])) : 'N/A',
                'Login Location' => $record['login_location'] ?: 'N/A',
                'Logout Location' => $record['logout_location'] ?: 'N/A',
                'Attendance Photo URL' => $record['attendance_photo_url'] ?: 'N/A',
            ];
        });

        $html = view('pages.hrms.attendance.export', [
            'rows' => $rows,
            'selectedDate' => $selectedDate,
        ])->render();

        $fileName = 'attendance_' . $selectedDate->format('Y_m_d') . '.xls';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function validateAttendanceFilters(Request $request): array
    {
        return $request->validate([
            'employee_name' => ['nullable', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:255'],
            'attendance_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:present,absent'],
            'login_timing' => ['nullable', 'in:early,late'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
    }

    private function buildAttendanceData(array $validated): array
    {
        $selectedDate = $validated['attendance_date'] ?? now()->toDateString();
        $employeeNameFilter = trim((string) ($validated['employee_name'] ?? ''));
        $employeeIdFilter = trim((string) ($validated['employee_id'] ?? ''));
        $statusFilter = $validated['status'] ?? '';
        $loginTimingFilter = $validated['login_timing'] ?? '';

        $attendanceCollection = DailyAttendance::query()
            ->whereDate('attendance_date', $selectedDate)
            ->orderBy('login_time')
            ->get();

        $presentRecords = $attendanceCollection->map(function (DailyAttendance $attendance) {
            return [
                'employee_id' => (string) $attendance->employee_id,
                'employee_name' => $attendance->employee_name ?: 'Unknown Employee',
                'attendance_date' => optional($attendance->attendance_date)->format('Y-m-d'),
                'attendance_status' => strtolower((string) $attendance->attendance_status) ?: 'present',
                'login_time' => $attendance->login_time,
                'logout_time' => $attendance->logout_time,
                'overall_working_hours' => $attendance->overall_working_hours,
                'login_location' => $attendance->login_location,
                'logout_location' => $attendance->logout_location,
                'remarks' => $attendance->remarks,
                'attendance_photo_url' => $attendance->attendance_photo ? asset($attendance->attendance_photo) : null,
                'login_timing' => $this->resolveLoginTiming($attendance->login_time),
                'is_derived' => false,
            ];
        });

        $presentEmployeeNames = $presentRecords
            ->pluck('employee_name')
            ->map(fn ($name) => $this->normalizeValue($name))
            ->filter()
            ->unique()
            ->values();

        $absentRecords = EmployeeOnboarding::query()
            ->select(['employee_id', 'name', 'status'])
            ->whereNotNull('name')
            ->get()
            ->reject(function (EmployeeOnboarding $employee) use ($presentEmployeeNames) {
                return $presentEmployeeNames->contains($this->normalizeValue($employee->name));
            })
            ->map(function (EmployeeOnboarding $employee) use ($selectedDate) {
                return [
                    'employee_id' => (string) $employee->employee_id,
                    'employee_name' => $employee->name,
                    'attendance_date' => $selectedDate,
                    'attendance_status' => 'absent',
                    'login_time' => null,
                    'logout_time' => null,
                    'overall_working_hours' => null,
                    'login_location' => null,
                    'logout_location' => null,
                    'remarks' => 'No check-in record found for the selected date.',
                    'attendance_photo_url' => null,
                    'login_timing' => null,
                    'is_derived' => true,
                ];
            });

        $stats = [
            'total_employees' => EmployeeOnboarding::count(),
            'present_count' => $presentRecords->count(),
            'absent_count' => $absentRecords->count(),
            'late_count' => $presentRecords->where('login_timing', 'late')->count(),
            'early_count' => $presentRecords->where('login_timing', 'early')->count(),
        ];

        $records = match ($statusFilter) {
            'present' => $presentRecords,
            'absent' => $absentRecords,
            default => $presentRecords->concat($absentRecords),
        };

        $records = $records
            ->filter(function (array $record) use ($employeeNameFilter, $employeeIdFilter, $loginTimingFilter) {
                if ($employeeNameFilter !== '' && ! str_contains($this->normalizeValue($record['employee_name']), $this->normalizeValue($employeeNameFilter))) {
                    return false;
                }

                if ($employeeIdFilter !== '' && ! str_contains($this->normalizeValue($record['employee_id']), $this->normalizeValue($employeeIdFilter))) {
                    return false;
                }

                if ($loginTimingFilter !== '' && ($record['login_timing'] ?? null) !== $loginTimingFilter) {
                    return false;
                }

                return true;
            })
            ->sortBy(fn (array $record) => sprintf(
                '%s|%s|%s',
                $record['attendance_status'] === 'absent' ? '1' : '0',
                $record['login_time'] ?? '23:59:59',
                $this->normalizeValue($record['employee_name'])
            ))
            ->values();

        return [
            'records' => $records,
            'selected_date' => $selectedDate,
            'stats' => $stats,
        ];
    }

    private function resolveLoginTiming(?string $loginTime): ?string
    {
        if (! $loginTime) {
            return null;
        }

        if ($loginTime <= self::EARLY_LOGIN_BEFORE) {
            return 'early';
        }

        if ($loginTime > self::LATE_LOGIN_AFTER) {
            return 'late';
        }

        return 'on-time';
    }

    private function normalizeValue(?string $value): string
    {
        return strtolower(trim((string) $value));
    }

    private function paginateCollection(Collection $items, int $perPage, int $page, Request $request): LengthAwarePaginator
    {
        $total = $items->count();
        $results = $items->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
