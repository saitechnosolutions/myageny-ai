<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInternJoiningFormRequest;
use App\Http\Requests\UpdateInternJoiningFormRequest;
use App\Models\InternDocument;
use App\Models\InternJoiningForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InternJoiningFormController extends Controller
{
    private const FILE_DIRECTORY = 'interns';

    private const DOCUMENT_LABELS = [
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
        $forms = InternJoiningForm::query()
            ->with('documents')
            ->when($request->search, function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('mobile', 'like', '%' . $search . '%')
                        ->orWhere('aadhaar_card_no', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.hrms.Interns.intern_joining_forms.index', compact('forms'));
    }

    public function create(): View
    {
        return view('pages.hrms.Interns.intern_joining_forms.create', [
            'documentLabels' => self::DOCUMENT_LABELS,
        ]);
    }

    public function store(StoreInternJoiningFormRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $form = DB::transaction(function () use ($request, $validated) {
            $form = InternJoiningForm::create($this->extractMainAttributes($validated, $request));
            $this->syncEducationalDetails($form, $validated['educational_details'] ?? []);
            $this->syncEmploymentDetails($form, $validated['employment_details'] ?? []);
            $this->syncFamilyDetails($form, $validated['family_details'] ?? []);
            $this->syncDocuments($form, $request);

            return $form;
        });

        return redirect()
            ->route('interns.show', $form)
            ->with('success', "Intern joining form for <strong>{$form->name}</strong> created successfully.");
    }

    public function show(InternJoiningForm $intern_joining_form): View
    {
        $intern_joining_form->load(['educationalDetails', 'employmentDetails', 'familyDetails', 'documents']);

        return view('pages.hrms.Interns.intern_joining_forms.show', [
            'form' => $intern_joining_form,
            'documentLabels' => self::DOCUMENT_LABELS,
        ]);
    }

    public function edit(InternJoiningForm $intern_joining_form): View
    {
        $intern_joining_form->load(['educationalDetails', 'employmentDetails', 'familyDetails', 'documents']);

        return view('pages.hrms.Interns.intern_joining_forms.edit', [
            'form' => $intern_joining_form,
            'documentLabels' => self::DOCUMENT_LABELS,
        ]);
    }

    public function update(UpdateInternJoiningFormRequest $request, InternJoiningForm $intern_joining_form): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated, $intern_joining_form) {
            $intern_joining_form->update($this->extractMainAttributes($validated, $request, $intern_joining_form));
            $this->syncEducationalDetails($intern_joining_form, $validated['educational_details'] ?? []);
            $this->syncEmploymentDetails($intern_joining_form, $validated['employment_details'] ?? []);
            $this->syncFamilyDetails($intern_joining_form, $validated['family_details'] ?? []);
            $this->syncDocuments($intern_joining_form, $request, true);
        });

        return redirect()
            ->route('interns.show', $intern_joining_form)
            ->with('success', "Intern joining form for <strong>{$intern_joining_form->name}</strong> updated successfully.");
    }

    public function destroy(InternJoiningForm $intern_joining_form): RedirectResponse
    {
        $name = $intern_joining_form->name;

        DB::transaction(function () use ($intern_joining_form) {
            $this->deleteStoredFile($intern_joining_form->photograph);

            if ($intern_joining_form->documents) {
                foreach (InternJoiningForm::DOCUMENT_FIELDS as $field) {
                    $this->deleteStoredFile($intern_joining_form->documents->{$field});
                }
            }

            $intern_joining_form->educationalDetails()->delete();
            $intern_joining_form->employmentDetails()->delete();
            $intern_joining_form->familyDetails()->delete();
            $intern_joining_form->documents()?->delete();
            $intern_joining_form->delete();
        });

        return redirect()
            ->route('interns.index')
            ->with('success', "Intern joining form for <strong>{$name}</strong> deleted successfully.");
    }

    private function extractMainAttributes(array $validated, Request $request, ?InternJoiningForm $form = null): array
    {
        $attributes = Arr::only($validated, [
            'name',
            'father_name',
            'correspondence_address',
            'permanent_address',
            'mobile',
            'email',
            'date_of_birth',
            'blood_group',
            'marital_status',
            'date_of_marriage',
            'aadhaar_card_no',
            'pan_card_no',
            'emergency_contact_name',
            'emergency_contact_relation',
            'emergency_contact_no',
            'declaration_accepted',
            'declaration_date',
            'declaration_place',
        ]);

        if (($attributes['marital_status'] ?? null) !== 'married') {
            $attributes['date_of_marriage'] = null;
        }

        if ($request->hasFile('photograph')) {
            if ($form?->photograph) {
                $this->deleteStoredFile($form->photograph);
            }

            $attributes['photograph'] = $request->file('photograph')->store(self::FILE_DIRECTORY . '/photographs', 'public');
        }

        return $attributes;
    }

    private function syncEducationalDetails(InternJoiningForm $form, array $rows): void
    {
        $form->educationalDetails()->delete();

        $preparedRows = collect(array_values($rows))
            ->map(fn (array $row, int $index) => [
                'qualification' => trim((string) ($row['qualification'] ?? '')),
                'institution_name' => trim((string) ($row['institution_name'] ?? '')),
                'year_of_passing' => trim((string) ($row['year_of_passing'] ?? '')),
                'percentage' => trim((string) ($row['percentage'] ?? '')),
                'specialization' => trim((string) ($row['specialization'] ?? '')),
                'sort_order' => $index + 1,
            ])
            ->all();

        $form->educationalDetails()->createMany($preparedRows);
    }

    private function syncEmploymentDetails(InternJoiningForm $form, array $rows): void
    {
        $form->employmentDetails()->delete();

        $preparedRows = collect(array_values($rows))
            ->filter(fn ($row) => $this->rowHasData((array) $row, ['organisation', 'designation', 'period_from', 'period_to', 'annual_ctc']))
            ->map(fn (array $row, int $index) => [
                'organisation' => trim((string) ($row['organisation'] ?? '')),
                'designation' => trim((string) ($row['designation'] ?? '')),
                'period_from' => $row['period_from'] ?: null,
                'period_to' => $row['period_to'] ?: null,
                'annual_ctc' => trim((string) ($row['annual_ctc'] ?? '')),
                'sort_order' => $index + 1,
            ])
            ->values()
            ->all();

        if ($preparedRows !== []) {
            $form->employmentDetails()->createMany($preparedRows);
        }
    }

    private function syncFamilyDetails(InternJoiningForm $form, array $rows): void
    {
        $form->familyDetails()->delete();

        $preparedRows = collect(array_values($rows))
            ->filter(fn ($row) => $this->rowHasData((array) $row, ['name', 'relation', 'occupation', 'date_of_birth', 'mobile_no']))
            ->map(fn (array $row, int $index) => [
                'name' => trim((string) ($row['name'] ?? '')),
                'relation' => trim((string) ($row['relation'] ?? '')),
                'occupation' => trim((string) ($row['occupation'] ?? '')),
                'date_of_birth' => $row['date_of_birth'] ?: null,
                'mobile_no' => trim((string) ($row['mobile_no'] ?? '')),
                'sort_order' => $index + 1,
            ])
            ->values()
            ->all();

        if ($preparedRows !== []) {
            $form->familyDetails()->createMany($preparedRows);
        }
    }

    private function syncDocuments(InternJoiningForm $form, Request $request, bool $isUpdate = false): void
    {
        $documents = $form->documents ?: new InternDocument();
        $documents->intern_joining_form_id = $form->id;

        foreach (InternJoiningForm::DOCUMENT_FIELDS as $field) {
            if ($field === 'signature_path') {
                continue;
            }

            if (! $request->hasFile($field)) {
                continue;
            }

            if ($isUpdate && $documents->{$field}) {
                $this->deleteStoredFile($documents->{$field});
            }

            $documents->{$field} = $request->file($field)->store(self::FILE_DIRECTORY . '/documents', 'public');
        }

        if ($request->hasFile('signature_upload')) {
            if ($isUpdate && $documents->signature_path) {
                $this->deleteStoredFile($documents->signature_path);
            }

            $documents->signature_path = $request->file('signature_upload')->store(self::FILE_DIRECTORY . '/signatures', 'public');
        } elseif ($signatureData = $request->input('signature_data')) {
            if ($isUpdate && $documents->signature_path) {
                $this->deleteStoredFile($documents->signature_path);
            }

            $storedPath = $this->storeSignatureData($signatureData);
            if ($storedPath !== '') {
                $documents->signature_path = $storedPath;
            }
        }

        $documents->save();
    }

    private function storeSignatureData(string $signatureData): string
    {
        if (! preg_match('/^data:image\/png;base64,/', $signatureData)) {
            return '';
        }

        $encoded = substr($signatureData, strpos($signatureData, ',') + 1);
        $binary = base64_decode($encoded, true);

        if ($binary === false) {
            return '';
        }

        $path = self::FILE_DIRECTORY . '/signatures/' . Str::uuid() . '.png';
        Storage::disk('public')->put($path, $binary);

        return $path;
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
}
