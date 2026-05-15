<?php

namespace App\Http\Controllers;

use App\Models\RecruitmentCallUpdate;
use App\Models\RecruitmentCandidate;
use App\Models\RecruitmentInterview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RecruitmentController extends Controller
{
    private const RESUME_DIRECTORY = 'recruitment/resumes';

    public function index(Request $request): View
    {
        $query = RecruitmentCandidate::query()
            ->with(['creator'])
            ->withCount(['callUpdates', 'interviews'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('candidate_no', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('job_title', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bucket')) {
            match ($request->bucket) {
                'selected' => $query->where('status', RecruitmentCandidate::STATUS_SELECTED),
                'rejected' => $query->where('status', RecruitmentCandidate::STATUS_REJECTED),
                'active' => $query->whereNotIn('status', [
                    RecruitmentCandidate::STATUS_SELECTED,
                    RecruitmentCandidate::STATUS_REJECTED,
                ]),
                default => null,
            };
        }

        $candidates = $query->paginate(12)->withQueryString();

        $counts = [
            'all' => RecruitmentCandidate::count(),
            'active' => RecruitmentCandidate::whereNotIn('status', [
                RecruitmentCandidate::STATUS_SELECTED,
                RecruitmentCandidate::STATUS_REJECTED,
            ])->count(),
            'selected' => RecruitmentCandidate::where('status', RecruitmentCandidate::STATUS_SELECTED)->count(),
            'rejected' => RecruitmentCandidate::where('status', RecruitmentCandidate::STATUS_REJECTED)->count(),
        ];

        return view('pages.hrms.recruitment.index', [
            'candidates' => $candidates,
            'counts' => $counts,
            'statuses' => RecruitmentCandidate::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('pages.hrms.recruitment.create', [
            'candidateNo' => RecruitmentCandidate::generateCandidateNo(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'mobile_number' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'job_title' => ['required', 'string', 'max:150'],
            'source' => ['nullable', 'string', 'max:100'],
            'current_ctc' => ['nullable', 'numeric', 'min:0'],
            'expected_ctc' => ['nullable', 'numeric', 'min:0'],
            'notice_period' => ['nullable', 'string', 'max:100'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'remarks' => ['nullable', 'string', 'max:3000'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        if ($request->hasFile('resume')) {
            $validated['resume_path'] = $request->file('resume')->store(self::RESUME_DIRECTORY, 'public');
        }

        unset($validated['resume']);

        $candidate = RecruitmentCandidate::create(array_merge($validated, [
            'candidate_no' => RecruitmentCandidate::generateCandidateNo(),
            'status' => RecruitmentCandidate::STATUS_APPLIED,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'status_updated_at' => now(),
        ]));

        return redirect()
            ->route('recruitment.show', $candidate)
            ->with('success', "Candidate <strong>{$candidate->name}</strong> added to recruitment.");
    }

    public function show(RecruitmentCandidate $recruitment): View
    {
        $recruitment->load(['callUpdates.user', 'interviews.scheduler', 'creator', 'updater']);

        return view('pages.hrms.recruitment.show', [
            'candidate' => $recruitment,
            'statuses' => RecruitmentCandidate::STATUSES,
            'callTypes' => RecruitmentCallUpdate::CALL_TYPES,
            'callOutcomes' => RecruitmentCallUpdate::OUTCOMES,
            'interviewModes' => RecruitmentInterview::MODES,
            'interviewStatuses' => RecruitmentInterview::STATUSES,
        ]);
    }

    public function storeCallUpdate(Request $request, RecruitmentCandidate $recruitment): RedirectResponse
    {
        $validated = $request->validate([
            'called_at' => ['required', 'date'],
            'call_type' => ['required', Rule::in(array_keys(RecruitmentCallUpdate::CALL_TYPES))],
            'duration_minutes' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'outcome' => ['required', Rule::in(array_keys(RecruitmentCallUpdate::OUTCOMES))],
            'notes' => ['nullable', 'string', 'max:3000'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $recruitment->callUpdates()->create(array_merge($validated, [
            'company_id' => auth()->user()?->company_id,
            'user_id' => auth()->id(),
        ]));

        $this->syncCandidateStatusFromCallOutcome($recruitment, $validated['outcome']);

        return back()->with('success', 'Call update added successfully.');
    }

    public function storeInterview(Request $request, RecruitmentCandidate $recruitment): RedirectResponse
    {
        $validated = $request->validate([
            'scheduled_at' => ['required', 'date'],
            'round' => ['nullable', 'string', 'max:80'],
            'mode' => ['required', Rule::in(array_keys(RecruitmentInterview::MODES))],
            'interviewer_name' => ['nullable', 'string', 'max:150'],
            'interview_link' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(RecruitmentInterview::STATUSES))],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $recruitment->interviews()->create(array_merge($validated, [
            'company_id' => auth()->user()?->company_id,
            'scheduled_by' => auth()->id(),
        ]));

        if (! in_array($recruitment->status, [
            RecruitmentCandidate::STATUS_SELECTED,
            RecruitmentCandidate::STATUS_REJECTED,
        ], true)) {
            $this->updateCandidateStatus($recruitment, RecruitmentCandidate::STATUS_INTERVIEW_SCHEDULED);
        }

        return back()->with('success', 'Interview scheduled successfully.');
    }

    public function updateStatus(Request $request, RecruitmentCandidate $recruitment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(RecruitmentCandidate::STATUSES))],
        ]);

        $this->updateCandidateStatus($recruitment, $validated['status']);

        return back()->with('success', 'Candidate moved to ' . $recruitment->fresh()->status_label . '.');
    }

    public function destroy(RecruitmentCandidate $recruitment): RedirectResponse
    {
        $name = $recruitment->name;

        if ($recruitment->resume_path && Storage::disk('public')->exists($recruitment->resume_path)) {
            Storage::disk('public')->delete($recruitment->resume_path);
        }

        $recruitment->delete();

        return redirect()
            ->route('recruitment.index')
            ->with('success', "Candidate <strong>{$name}</strong> deleted successfully.");
    }

    private function syncCandidateStatusFromCallOutcome(RecruitmentCandidate $candidate, string $outcome): void
    {
        $status = match ($outcome) {
            'screening', 'interested', 'follow_up' => RecruitmentCandidate::STATUS_SCREENING,
            'interview_planned' => RecruitmentCandidate::STATUS_INTERVIEW_SCHEDULED,
            'selected' => RecruitmentCandidate::STATUS_SELECTED,
            'rejected', 'not_interested' => RecruitmentCandidate::STATUS_REJECTED,
            default => null,
        };

        if ($status) {
            $this->updateCandidateStatus($candidate, $status);
        }
    }

    private function updateCandidateStatus(RecruitmentCandidate $candidate, string $status): void
    {
        $candidate->update([
            'status' => $status,
            'updated_by' => auth()->id(),
            'status_updated_at' => now(),
        ]);
    }
}
