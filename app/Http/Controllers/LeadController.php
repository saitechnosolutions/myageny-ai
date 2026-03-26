<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Branch;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * List all leads with full filter support.
     */
    public function index(Request $request)
    {
        $query = Lead::with(['branch', 'assignedTo', 'createdBy'])
            ->latest('lead_date');

        // ── Filters ──────────────────────────────────────────────
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('company_name',  'like', "%{$s}%")
                  ->orWhere('contact_name','like', "%{$s}%")
                  ->orWhere('mobile_number','like',"%{$s}%")
                  ->orWhere('email',        'like', "%{$s}%")
                  ->orWhere('product_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('mobile_number')) {
            $query->where('mobile_number', 'like', '%' . $request->mobile_number . '%');
        }

        if ($request->filled('lead_source')) {
            $query->where('lead_source', $request->lead_source);
        }

        if ($request->filled('lead_status')) {
            $query->where('lead_status', $request->lead_status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('product_name')) {
            $query->where('product_name', 'like', '%' . $request->product_name . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('lead_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('lead_date', '<=', $request->date_to);
        }

        $leads    = $query->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $users    = User::where('is_active', true)->orderBy('name')->get();
        $products = Lead::select('product_name')->whereNotNull('product_name')->distinct()->pluck('product_name');

        // Stats for top cards
        $stats = [
            'total'       => Lead::count(),
            'new'         => Lead::where('lead_status', 'new')->count(),
            'won'         => Lead::where('lead_status', 'won')->count(),
            'lost'        => Lead::where('lead_status', 'lost')->count(),
            'pipeline'    => Lead::whereNotIn('lead_status', ['won','lost'])->sum('deal_value'),
            'high_priority'=> Lead::where('priority', 'high')->whereNotIn('lead_status', ['won','lost'])->count(),
        ];

        return view('pages.leads.index', compact('leads', 'branches', 'users', 'products', 'stats'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $users    = User::where('is_active', true)->orderBy('name')->get();

        return view('pages.leads.create', compact('branches', 'users'));
    }

    /**
     * Store new lead.
     */
    public function store(StoreLeadRequest $request)
    {
        $lead = Lead::create($request->validated());

        return redirect()
            ->route('leads.show', $lead)
            ->with('success', "Lead for <strong>{$lead->company_name}</strong> created successfully.");
    }

    /**
     * Show single lead.
     */
    public function show(Lead $lead)
    {
        $lead->load(['branch', 'assignedTo', 'createdBy']);

        return view('pages.leads.show', compact('lead'));
    }

    /**
     * Show edit form.
     */
    public function edit(Lead $lead)
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $users    = User::where('is_active', true)->orderBy('name')->get();

        return view('pages.leads.edit', compact('lead', 'branches', 'users'));
    }

    /**
     * Update lead.
     */
    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $lead->update($request->validated());

        return redirect()
            ->route('leads.show', $lead)
            ->with('success', "Lead <strong>{$lead->company_name}</strong> updated successfully.");
    }

    /**
     * Soft-delete lead.
     */
    public function destroy(Lead $lead)
    {
        $name = $lead->company_name;
        $lead->delete();

        return redirect()
            ->route('leads.index')
            ->with('success', "Lead <strong>{$name}</strong> has been removed.");
    }

    /**
     * Quick status update (AJAX-friendly PATCH).
     */
    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'lead_status' => ['required', 'in:' . implode(',', array_keys(Lead::STATUSES))],
        ]);

        $lead->update(['lead_status' => $request->lead_status]);

        return back()->with('success', "Lead status updated to <strong>{$lead->status_label}</strong>.");
    }
}