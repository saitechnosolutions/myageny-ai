<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacilityManagementRequest;
use App\Models\FacilityManagement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityManagementController extends Controller
{
    public function index(Request $request): View
    {
        $facilityEntries = FacilityManagement::query()
            ->when($request->search, function ($query) use ($request) {
                $search = trim((string) $request->search);
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('pages.settings.facility_management.index', compact('facilityEntries'));
    }

    public function create(): View
    {
        return view('pages.settings.facility_management.create');
    }

    public function store(FacilityManagementRequest $request): RedirectResponse
    {
        $facility = FacilityManagement::create($request->validated());

        return redirect()
            ->route('facility-management.index')
            ->with('success', "Facility entry '{$facility->title}' created successfully.");
    }

    public function edit(FacilityManagement $facility_management): View
    {
        return view('pages.settings.facility_management.edit', [
            'facilityEntry' => $facility_management,
        ]);
    }

    public function update(FacilityManagementRequest $request, FacilityManagement $facility_management): RedirectResponse
    {
        $facility_management->update($request->validated());

        return redirect()
            ->route('facility-management.index')
            ->with('success', "Facility entry '{$facility_management->title}' updated successfully.");
    }

    public function destroy(FacilityManagement $facility_management): RedirectResponse
    {
        $facility_management->delete();

        return redirect()
            ->route('facility-management.index')
            ->with('success', "Facility entry '{$facility_management->title}' deleted successfully.");
    }
}