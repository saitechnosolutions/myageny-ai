<?php

namespace App\Http\Controllers\App\HRMS;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmployeeOnboarding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeApiController extends Controller
{
    // ── GET /api/mobile/hrms/employees ────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $query = EmployeeOnboarding::query()
            ->with(['role', 'department'])
            ->when($request->search, function ($q) use ($request) {
                $s = trim((string) $request->search);
                $q->where(function ($sub) use ($s) {
                    $sub->where('employee_id', 'like', "%$s%")
                        ->orWhere('name',        'like', "%$s%")
                        ->orWhere('email',       'like', "%$s%")
                        ->orWhere('mobile',      'like', "%$s%")
                        ->orWhere('aadhaar_card_no', 'like', "%$s%");
                });
            })
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id))
            ->latest();

        $perPage    = (int) ($request->per_page ?? 15);
        $employees  = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => [
                'employees'  => $employees->map(fn ($e) => $this->mapList($e)),
                'pagination' => [
                    'current_page' => $employees->currentPage(),
                    'last_page'    => $employees->lastPage(),
                    'per_page'     => $employees->perPage(),
                    'total'        => $employees->total(),
                    'has_more'     => $employees->hasMorePages(),
                ],
            ],
        ]);
    }

    // ── GET /api/mobile/hrms/employees/{id} ───────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $employee = EmployeeOnboarding::with([
            'role',
            'department',
            'educations',
            'employments',
            'familyDetails',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->mapDetail($employee),
        ]);
    }

    // ── GET /api/mobile/hrms/employees/meta ───────────────────────────────────
    public function meta(): JsonResponse
    {
        $departments = Department::whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name']);

        $statuses = ['pending', 'verified', 'rejected'];

        return response()->json([
            'success' => true,
            'data'    => [
                'departments' => $departments,
                'statuses'    => $statuses,
            ],
        ]);
    }

    // ── Private: list shape (compact) ─────────────────────────────────────────
    private function mapList(EmployeeOnboarding $e): array
    {
        return [
            'id'             => $e->id,
            'employee_id'    => $e->employee_id,
            'name'           => $e->name,
            'email'          => $e->email,
            'mobile'         => $e->mobile,
            'role'           => optional($e->role)->display_name ?? optional($e->role)->name,
            'department'     => optional($e->department)->name,
            'status'         => $e->status,
            'avatar_initial' => strtoupper(substr($e->name, 0, 1)),
            'created_at'     => optional($e->created_at)->format('d M Y'),
        ];
    }

    // ── Private: detail shape (full) ──────────────────────────────────────────
    private function mapDetail(EmployeeOnboarding $e): array
    {
        return [
            // Identity
            'id'                       => $e->id,
            'employee_id'              => $e->employee_id,
            'name'                     => $e->name,
            'email'                    => $e->email,
            'mobile'                   => $e->mobile,
            'role'                     => optional($e->role)->display_name ?? optional($e->role)->name,
            'department'               => optional($e->department)->name,
            'status'                   => $e->status,
            'avatar_initial'           => strtoupper(substr($e->name, 0, 1)),

            // Personal
            'father_name'              => $e->father_name             ?? '',
            'date_of_birth'            => optional($e->date_of_birth)->format('d M Y') ?? '',
            'blood_group'              => $e->blood_group             ?? '',
            'marital_status'           => $e->marital_status          ?? '',
            'aadhaar_card_no'          => $e->aadhaar_card_no         ?? '',
            'pan_card_no'              => $e->pan_card_no             ?? '',
            'correspondence_address'   => $e->correspondence_address  ?? '',
            'permanent_address'        => $e->permanent_address       ?? '',

            // Emergency
            'emergency_contact_name'   => $e->emergency_contact_name  ?? '',
            'emergency_relation'       => $e->emergency_relation       ?? '',
            'emergency_contact_no'     => $e->emergency_contact_no     ?? '',

            // Salary
            'gross_salary'             => $e->gross_salary             ? (float) $e->gross_salary : null,
            'net_salary'               => $e->net_salary               ? (float) $e->net_salary   : null,
            'salary_payment_mode'      => $e->salary_payment_mode      ?? '',
            'pf_enabled'               => (bool) ($e->pf_enabled       ?? false),
            'esi_enabled'              => (bool) ($e->esi_enabled       ?? false),

            // Bank
            'bank_name'                => $e->bank_name                ?? '',
            'bank_account_no'          => $e->bank_account_no          ?? '',
            'bank_ifsc'                => $e->bank_ifsc                 ?? '',
            'bank_branch'              => $e->bank_branch               ?? '',

            // Relations
            'educations'  => $e->educations->map(fn ($ed) => [
                'qualification'  => $ed->qualification  ?? '',
                'institution'    => $ed->institution_name ?? '',
                'year'           => $ed->year_of_passing ?? '',
                'percentage'     => $ed->percentage      ?? '',
                'specialization' => $ed->specialization  ?? '',
            ])->values(),

            'employments' => $e->employments->map(fn ($em) => [
                'organisation' => $em->organisation   ?? '',
                'designation'  => $em->designation    ?? '',
                'period_from'  => optional($em->period_from)->format('d M Y') ?? ($em->period_from ?? ''),
                'period_to'    => optional($em->period_to)->format('d M Y')   ?? ($em->period_to   ?? ''),
                'annual_ctc'   => $em->annual_ctc      ?? '',
            ])->values(),

            'family_details' => $e->familyDetails->map(fn ($f) => [
                'name'         => $f->name          ?? '',
                'relation'     => $f->relation       ?? '',
                'occupation'   => $f->occupation     ?? '',
                'date_of_birth'=> optional($f->date_of_birth)->format('d M Y') ?? '',
                'mobile_no'    => $f->mobile_no      ?? '',
            ])->values(),

            'created_at' => optional($e->created_at)->format('d M Y'),
        ];
    }
}