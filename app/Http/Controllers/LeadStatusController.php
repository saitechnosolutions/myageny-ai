<?php
// app/Http/Controllers/Settings/LeadStatusController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeadStatusRequest;
use App\Models\LeadStatus;
use Illuminate\Http\Request;

class LeadStatusController extends Controller
{
    public function index()
    {
        $statuses = LeadStatus::latest()->get();
        return view('pages.settings.lead-status.index', compact('statuses'));
    }

    public function store(LeadStatusRequest $request)
    {
        LeadStatus::create([
            'name'       => $request->name,
        ]);

        return redirect()->route('settings.lead-statuses.index')
                         ->with('success', 'Lead Status created successfully.');
    }

    public function edit(LeadStatus $leadStatus)
    {
        $this->authorizeCompany($leadStatus);
        return view('pages.settings.lead-status.edit', compact('leadStatus'));
    }

    public function update(LeadStatusRequest $request, LeadStatus $leadStatus)
    {
        $this->authorizeCompany($leadStatus);
        $leadStatus->update(['name' => $request->name]);

        return redirect()->route('settings.lead-statuses.index')
                         ->with('success', 'Lead Status updated successfully.');
    }

    public function destroy(LeadStatus $leadStatus)
    {
        $this->authorizeCompany($leadStatus);
        $leadStatus->delete();

        return redirect()->route('settings.lead-statuses.index')
                         ->with('success', 'Lead Status deleted.');
    }

    private function authorizeCompany($model)
    {
        if ($model->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
