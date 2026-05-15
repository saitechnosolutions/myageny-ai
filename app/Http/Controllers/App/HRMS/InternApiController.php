<?php

namespace App\Http\Controllers\App\HRMS;

use App\Http\Controllers\Controller;
use App\Models\InternJoiningForm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InternApiController extends Controller
{
    // ── GET /api/mobile/hrms/interns ──────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $query = InternJoiningForm::query()
            ->when($request->search, function ($q) use ($request) {
                $s = trim((string) $request->search);
                $q->where(function ($sub) use ($s) {
                    $sub->where('name',           'like', "%$s%")
                        ->orWhere('email',         'like', "%$s%")
                        ->orWhere('mobile',        'like', "%$s%")
                        ->orWhere('aadhaar_card_no','like', "%$s%");
                });
            })
            ->latest();

        $perPage = (int) ($request->per_page ?? 15);
        $interns = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => [
                'interns'    => $interns->map(fn ($i) => $this->mapList($i)),
                'pagination' => [
                    'current_page' => $interns->currentPage(),
                    'last_page'    => $interns->lastPage(),
                    'per_page'     => $interns->perPage(),
                    'total'        => $interns->total(),
                    'has_more'     => $interns->hasMorePages(),
                ],
            ],
        ]);
    }

    // ── GET /api/mobile/hrms/interns/{id} ─────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $intern = InternJoiningForm::with([
            'educationalDetails',
            'employmentDetails',
            'familyDetails',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->mapDetail($intern),
        ]);
    }

    // ── Private: list shape (compact) ─────────────────────────────────────────
    private function mapList(InternJoiningForm $i): array
    {
        return [
            'id'              => $i->id,
            'name'            => $i->name,
            'email'           => $i->email,
            'mobile'          => $i->mobile,
            'father_name'     => $i->father_name      ?? '',
            'date_of_birth'   => optional($i->date_of_birth)->format('d M Y') ?? '',
            'age'             => $i->date_of_birth
                                    ? $i->date_of_birth->age . ' yrs'
                                    : '',
            'declaration_date'=> optional($i->declaration_date)->format('d M Y') ?? '',
            'avatar_initial'  => strtoupper(substr($i->name, 0, 1)),
            'created_at'      => optional($i->created_at)->format('d M Y'),
        ];
    }

    // ── Private: detail shape (full) ──────────────────────────────────────────
    private function mapDetail(InternJoiningForm $i): array
    {
        return [
            'id'                       => $i->id,
            'name'                     => $i->name,
            'email'                    => $i->email,
            'mobile'                   => $i->mobile,
            'avatar_initial'           => strtoupper(substr($i->name, 0, 1)),

            // Personal
            'father_name'              => $i->father_name             ?? '',
            'date_of_birth'            => optional($i->date_of_birth)->format('d M Y') ?? '',
            'age'                      => $i->date_of_birth ? $i->date_of_birth->age . ' yrs' : '',
            'blood_group'              => $i->blood_group             ?? '',
            'marital_status'           => $i->marital_status          ?? '',
            'aadhaar_card_no'          => $i->aadhaar_card_no         ?? '',
            'pan_card_no'              => $i->pan_card_no             ?? '',
            'correspondence_address'   => $i->correspondence_address  ?? '',
            'permanent_address'        => $i->permanent_address       ?? '',

            // Emergency
            'emergency_contact_name'   => $i->emergency_contact_name      ?? '',
            'emergency_contact_relation'=> $i->emergency_contact_relation  ?? '',
            'emergency_contact_no'     => $i->emergency_contact_no         ?? '',

            // Declaration
            'declaration_accepted'     => (bool) ($i->declaration_accepted ?? false),
            'declaration_date'         => optional($i->declaration_date)->format('d M Y') ?? '',
            'declaration_place'        => $i->declaration_place ?? '',

            // Relations
            'educations' => $i->educationalDetails->map(fn ($e) => [
                'qualification'  => $e->qualification    ?? '',
                'institution'    => $e->institution_name ?? '',
                'year'           => $e->year_of_passing  ?? '',
                'percentage'     => $e->percentage       ?? '',
                'specialization' => $e->specialization   ?? '',
            ])->values(),

            'employments' => $i->employmentDetails->map(fn ($e) => [
                'organisation' => $e->organisation ?? '',
                'designation'  => $e->designation  ?? '',
                'period_from'  => optional($e->period_from)->format('d M Y') ?? ($e->period_from ?? ''),
                'period_to'    => optional($e->period_to)->format('d M Y')   ?? ($e->period_to   ?? ''),
                'annual_ctc'   => $e->annual_ctc   ?? '',
            ])->values(),

            'family_details' => $i->familyDetails->map(fn ($f) => [
                'name'         => $f->name          ?? '',
                'relation'     => $f->relation      ?? '',
                'occupation'   => $f->occupation    ?? '',
                'date_of_birth'=> optional($f->date_of_birth)->format('d M Y') ?? '',
                'mobile_no'    => $f->mobile_no     ?? '',
            ])->values(),

            'created_at' => optional($i->created_at)->format('d M Y'),
        ];
    }
}