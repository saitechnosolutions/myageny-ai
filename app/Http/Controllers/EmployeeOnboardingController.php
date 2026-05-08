<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeOnboardingRequest;
use App\Http\Requests\UpdateEmployeeOnboardingRequest;
use App\Models\Department;
use App\Models\EmployeeOnboarding;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EmployeeOnboardingController extends Controller
{
    private const FILE_DIRECTORY = 'employee_onboarding';
    private const EMPLOYEE_ID_PREFIX = 'STS';

    private const DOCUMENT_LABELS = [
        'photograph' => 'Photograph',
        'signature' => 'Signature',
        'document_10th_marksheet' => '10th Marksheet',
        'document_12th_marksheet' => '12th Marksheet',
        'document_consolidated_marksheet' => 'Consolidated Marksheet',
        'document_course_completion_certificate' => 'Course Completion Certificate',
        'document_degree_certificate' => 'Degree Certificate',
        'document_provisional_certificate' => 'Provisional Certificate',
        'document_tc' => 'TC',
        'document_aadhaar_card' => 'Aadhaar Card',
        'document_pan_card' => 'Pan Card',
        'document_voter_id' => 'Voter ID',
        'document_driving_licence' => 'Driving Licence',
        'document_experience_certificate' => 'Experience Certificate & Relieving Letter',
        'document_salary_slips' => 'Last 3 Salary Slips / Salary Certificate',
        'document_bank_passbook' => 'Bank Passbook',
    ];

    public function index(Request $request): View
    {
        $employees = EmployeeOnboarding::query()
            ->with(['role', 'department'])
            ->when($request->search, function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('employee_id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('mobile', 'like', '%' . $search . '%')
                        ->orWhere('aadhaar_card_no', 'like', '%' . $search . '%');
                });
            })
            ->when($request->status, fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.hrms.employee_onboarding.index', compact('employees'));
    }

    public function create(): View
    {
        return view('pages.hrms.employee_onboarding.create', [
            'documentLabels' => self::DOCUMENT_LABELS,
            'generatedEmployeeId' => $this->generateNextEmployeeId(),
            'roles' => Role::with('department')->orderByRaw('COALESCE(display_name, name)')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(StoreEmployeeOnboardingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $employee = DB::transaction(function () use ($request, $validated) {
            $employee = new EmployeeOnboarding();
            $employee->fill($this->extractAttributes($validated));
            $employee->employee_id = $this->generateNextEmployeeId();
            $employee->created_by = auth()->id();
            $employee->updated_by = auth()->id();
            $this->fillFileAttributes($employee, $request);
            $employee->save();

            $this->syncRelatedRows($employee, $validated);

            return $employee;
        });

        return redirect()
            ->route('employee-onboarding.show', $employee)
            ->with('success', "Employee onboarding for <strong>{$employee->name}</strong> created successfully.");
    }

    public function show(EmployeeOnboarding $employee_onboarding): View
    {
        $employee_onboarding->load(['educations', 'employments', 'familyDetails', 'creator', 'updater', 'role.department', 'department']);

        return view('pages.hrms.employee_onboarding.show', [
            'employee' => $employee_onboarding,
            'documentLabels' => self::DOCUMENT_LABELS,
        ]);
    }

    public function edit(EmployeeOnboarding $employee_onboarding): View
    {
        $employee_onboarding->load(['educations', 'employments', 'familyDetails', 'role.department', 'department']);

        return view('pages.hrms.employee_onboarding.edit', [
            'employee' => $employee_onboarding,
            'documentLabels' => self::DOCUMENT_LABELS,
            'generatedEmployeeId' => $employee_onboarding->employee_id,
            'roles' => Role::with('department')->orderByRaw('COALESCE(display_name, name)')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateEmployeeOnboardingRequest $request, EmployeeOnboarding $employee_onboarding): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated, $employee_onboarding) {
            $employee_onboarding->fill($this->extractAttributes($validated));
            $employee_onboarding->updated_by = auth()->id();
            $this->fillFileAttributes($employee_onboarding, $request, true);
            $employee_onboarding->save();

            $this->syncRelatedRows($employee_onboarding, $validated);
        });

        return redirect()
            ->route('employee-onboarding.show', $employee_onboarding)
            ->with('success', "Employee onboarding for <strong>{$employee_onboarding->name}</strong> updated successfully.");
    }

    public function destroy(EmployeeOnboarding $employee_onboarding): RedirectResponse
    {
        $employeeName = $employee_onboarding->name;

        DB::transaction(function () use ($employee_onboarding) {
            foreach (EmployeeOnboarding::DOCUMENT_FIELDS as $field) {
                $this->deleteStoredFile($employee_onboarding->{$field});
            }

            $employee_onboarding->educations()->delete();
            $employee_onboarding->employments()->delete();
            $employee_onboarding->familyDetails()->delete();
            $employee_onboarding->delete();
        });

        return redirect()
            ->route('employee-onboarding.index')
            ->with('success', "Employee onboarding for <strong>{$employeeName}</strong> deleted successfully.");
    }

    private function extractAttributes(array $validated): array
    {
        $attributes = Arr::except($validated, array_merge([
            'educations',
            'employments',
            'family_details',
            'employee_id',
        ], EmployeeOnboarding::DOCUMENT_FIELDS));

        if (($attributes['marital_status'] ?? null) !== 'married') {
            $attributes['date_of_marriage'] = null;
        }

        return $attributes;
    }

    private function fillFileAttributes(EmployeeOnboarding $employee, Request $request, bool $isUpdate = false): void
    {
        foreach (EmployeeOnboarding::DOCUMENT_FIELDS as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }

            if ($isUpdate) {
                $this->deleteStoredFile($employee->{$field});
            }

            $employee->{$field} = $request->file($field)->store(self::FILE_DIRECTORY, 'public');
        }
    }

    private function syncRelatedRows(EmployeeOnboarding $employee, array $validated): void
    {
        $educations = $this->sanitizeRows($validated['educations'] ?? [], [
            'qualification',
            'institution_name',
            'year_of_passing',
            'percentage',
            'specialization',
        ]);
        $employments = $this->sanitizeRows($validated['employments'] ?? [], [
            'organisation',
            'designation',
            'period_from',
            'period_to',
            'annual_ctc',
        ]);
        $familyDetails = $this->sanitizeRows($validated['family_details'] ?? [], [
            'name',
            'relation',
            'occupation',
            'date_of_birth',
            'mobile_no',
        ]);

        $employee->educations()->delete();
        $employee->employments()->delete();
        $employee->familyDetails()->delete();

        if ($educations !== []) {
            $employee->educations()->createMany($educations);
        }
        if ($employments !== []) {
            $employee->employments()->createMany($employments);
        }
        if ($familyDetails !== []) {
            $employee->familyDetails()->createMany($familyDetails);
        }
    }

    private function sanitizeRows(array $rows, array $keys): array
    {
        $cleanRows = [];

        foreach (array_values($rows) as $index => $row) {
            $row = is_array($row) ? $row : [];

            if (! $this->rowHasData($row, $keys)) {
                continue;
            }

            $cleanRows[] = collect($row)
                ->only($keys)
                ->map(function ($value) {
                    return is_string($value) ? trim($value) : $value;
                })
                ->put('sort_order', $index + 1)
                ->all();
        }

        return $cleanRows;
    }

    private function rowHasData(array $row, array $keys): bool
    {
        foreach ($keys as $key) {
            $value = $row[$key] ?? null;

            if ($value !== null && $value !== '') {
                return true;
            }
        }

        return false;
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function generateNextEmployeeId(): string
    {
        $latestEmployeeId = EmployeeOnboarding::query()
            ->where('employee_id', 'like', self::EMPLOYEE_ID_PREFIX . '%')
            ->orderByDesc('employee_id')
            ->lockForUpdate()
            ->value('employee_id');

        $lastNumber = 0;

        if ($latestEmployeeId && preg_match('/^' . preg_quote(self::EMPLOYEE_ID_PREFIX, '/') . '(\d+)$/', $latestEmployeeId, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return self::EMPLOYEE_ID_PREFIX . str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
    }
}
