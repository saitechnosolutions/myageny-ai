<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadReminder;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Leads", description: "Lead management endpoints for mobile app")]

class LeadController extends Controller
{
    // =========================================================================
    // INDEX — List all leads with filters + pagination
    // =========================================================================

    #[OA\Get(
        path: "/api/mobile/leads",
        summary: "List leads with filters & pagination",
        description: "Returns a paginated list of leads. Supports search, filtering by branch, source, status, priority, assigned user, product, and date range.",
        security: [["sanctum" => []]],
        tags: ["Leads"],
        parameters: [
            new OA\Parameter(name: "search",        in: "query", required: false, description: "Search across company name, contact name, mobile, email, product", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "branch_id",     in: "query", required: false, description: "Filter by branch ID",     schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "lead_source",   in: "query", required: false, description: "Filter by lead source",   schema: new OA\Schema(type: "string", enum: ["reference","ad_campaign","direct_visit","invitation","cold_outreach","social_media","website"])),
            new OA\Parameter(name: "lead_status",   in: "query", required: false, description: "Filter by lead status",   schema: new OA\Schema(type: "string", enum: ["new","qualified","proposal","negotiation","won","lost"])),
            new OA\Parameter(name: "priority",      in: "query", required: false, description: "Filter by priority",      schema: new OA\Schema(type: "string", enum: ["low","medium","high"])),
            new OA\Parameter(name: "assigned_to",   in: "query", required: false, description: "Filter by assigned user ID", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "mobile_number", in: "query", required: false, description: "Filter by mobile number (partial match)", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "product_name",  in: "query", required: false, description: "Filter by product name (partial match)",  schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "date_from",     in: "query", required: false, description: "Filter leads from this date (Y-m-d)",  schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "date_to",       in: "query", required: false, description: "Filter leads up to this date (Y-m-d)", schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "per_page",      in: "query", required: false, description: "Results per page (default 15, max 100)", schema: new OA\Schema(type: "integer", default: 15)),
            new OA\Parameter(name: "page",          in: "query", required: false, description: "Page number", schema: new OA\Schema(type: "integer", default: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Paginated list of leads",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "status", type: "boolean", example: true),
                    new OA\Property(property: "data",   type: "object", properties: [
                        new OA\Property(property: "leads", type: "object", properties: [
                            new OA\Property(property: "data",          type: "array", items: new OA\Items(ref: "#/components/schemas/LeadSummary")),
                            new OA\Property(property: "current_page",  type: "integer", example: 1),
                            new OA\Property(property: "last_page",     type: "integer", example: 5),
                            new OA\Property(property: "per_page",      type: "integer", example: 15),
                            new OA\Property(property: "total",         type: "integer", example: 72),
                        ]),
                        new OA\Property(property: "stats", ref: "#/components/schemas/LeadStats"),
                    ]),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/UnauthenticatedResponse")),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page'  => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Lead::with(['branch:id,name', 'assignedTo:id,name', 'createdBy:id,name'])
            ->latest('lead_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('company_name',   'like', "%{$s}%")
                  ->orWhere('contact_name', 'like', "%{$s}%")
                  ->orWhere('mobile_number','like', "%{$s}%")
                  ->orWhere('email',        'like', "%{$s}%")
                  ->orWhere('product_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('branch_id'))     $query->where('branch_id',    $request->branch_id);
        if ($request->filled('mobile_number')) $query->where('mobile_number','like', '%'.$request->mobile_number.'%');
        if ($request->filled('lead_source'))   $query->where('lead_source',  $request->lead_source);
        if ($request->filled('lead_status'))   $query->where('lead_status',  $request->lead_status);
        if ($request->filled('priority'))      $query->where('priority',     $request->priority);
        if ($request->filled('assigned_to'))   $query->where('assigned_to',  $request->assigned_to);
        if ($request->filled('product_name'))  $query->where('product_name', 'like', '%'.$request->product_name.'%');
        if ($request->filled('date_from'))     $query->whereDate('lead_date', '>=', $request->date_from);
        if ($request->filled('date_to'))       $query->whereDate('lead_date', '<=', $request->date_to);

        $perPage = (int) $request->input('per_page', 15);
        $leads   = $query->paginate($perPage);

        $stats = [
            'total'         => Lead::count(),
            'new'           => Lead::where('lead_status', 'new')->count(),
            'won'           => Lead::where('lead_status', 'won')->count(),
            'lost'          => Lead::where('lead_status', 'lost')->count(),
            'pipeline'      => Lead::whereNotIn('lead_status', ['won','lost'])->sum('deal_value'),
            'high_priority' => Lead::where('priority', 'high')->whereNotIn('lead_status', ['won','lost'])->count(),
        ];

        return response()->json([
            'status' => true,
            'data'   => [
                'leads' => [
                    'data'         => $leads->map(fn($l) => $this->formatLeadSummary($l)),
                    'current_page' => $leads->currentPage(),
                    'last_page'    => $leads->lastPage(),
                    'per_page'     => $leads->perPage(),
                    'total'        => $leads->total(),
                ],
                'stats' => $stats,
            ],
        ]);
    }

    // =========================================================================
    // STORE — Create a new lead (with optional reminder)
    // =========================================================================

    #[OA\Post(
        path: "/api/mobile/leads",
        summary: "Create a new lead",
        description: "Creates a new lead. Optionally accepts a `reminder` object — if provided, a reminder is automatically created and linked to the new lead.",
        security: [["sanctum" => []]],
        tags: ["Leads"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["company_name", "contact_name", "mobile_number", "lead_source", "lead_status", "priority"],
                properties: [
                    new OA\Property(property: "company_name",  type: "string",  example: "Acme Pvt Ltd"),
                    new OA\Property(property: "contact_name",  type: "string",  example: "Ravi Kumar"),
                    new OA\Property(property: "lead_date",     type: "string",  format: "date",  nullable: true, example: "2025-07-15", description: "Defaults to today if omitted"),
                    new OA\Property(property: "mobile_number", type: "string",  example: "9876543210"),
                    new OA\Property(property: "email",         type: "string",  format: "email", nullable: true, example: "ravi@acme.com"),
                    new OA\Property(property: "lead_source",   type: "string",  enum: ["reference","ad_campaign","direct_visit","invitation","cold_outreach","social_media","website"], example: "reference"),
                    new OA\Property(property: "lead_status",   type: "string",  enum: ["new","qualified","proposal","negotiation","won","lost"], example: "new"),
                    new OA\Property(property: "product_name",  type: "string",  nullable: true, example: "Solar Panel 10kW"),
                    new OA\Property(property: "priority",      type: "string",  enum: ["low","medium","high"], example: "high"),
                    new OA\Property(property: "deal_value",    type: "number",  format: "float", nullable: true, example: 250000.00),
                    new OA\Property(property: "remarks",       type: "string",  nullable: true,  example: "Customer interested in bulk order"),
                    new OA\Property(property: "branch_id",     type: "integer", nullable: true,  example: 2),
                    new OA\Property(property: "assigned_to",   type: "integer", nullable: true,  example: 5),
                    new OA\Property(
                        property: "reminder",
                        type: "object",
                        nullable: true,
                        description: "Optional reminder to create along with the lead",
                        required: ["remind_at", "title"],
                        properties: [
                            new OA\Property(property: "remind_at",   type: "string", format: "date-time", example: "2027-07-18T10:00:00"),
                            new OA\Property(property: "title",       type: "string", example: "Follow up call with Ravi"),
                            new OA\Property(property: "description", type: "string", nullable: true, example: "Discuss bulk order pricing"),
                            new OA\Property(property: "type",        type: "string", nullable: true, enum: ["follow_up","meeting","call","email","demo","other"], example: "call"),
                            new OA\Property(property: "priority",    type: "string", nullable: true, enum: ["low","medium","high"], example: "medium"),
                        ]
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Lead created successfully",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "status",           type: "boolean", example: true),
                    new OA\Property(property: "message",          type: "string",  example: "Lead created successfully."),
                    new OA\Property(property: "data",             ref: "#/components/schemas/LeadDetail"),
                    new OA\Property(property: "reminder_created", type: "boolean", example: true, description: "true if a reminder was also created"),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/UnauthenticatedResponse")),
            new OA\Response(response: 422, description: "Validation error",  content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_name'  => ['required', 'string', 'max:255'],
            'contact_name'  => ['required', 'string', 'max:255'],
            'lead_date'     => ['nullable', 'date'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'email'         => ['nullable', 'email', 'max:255'],
            'lead_source'   => ['required', 'string', 'in:' . implode(',', array_keys(Lead::SOURCES))],
            'lead_status'   => ['required', 'string', 'in:' . implode(',', array_keys(Lead::STATUSES))],
            'product_name'  => ['nullable', 'string', 'max:255'],
            'priority'      => ['required', 'string', 'in:' . implode(',', array_keys(Lead::PRIORITIES))],
            'deal_value'    => ['nullable', 'numeric', 'min:0'],
            'remarks'       => ['nullable', 'string'],
            'branch_id'     => ['nullable', 'integer', 'exists:branches,id'],
            'assigned_to'   => ['nullable', 'integer', 'exists:users,id'],

            // Optional reminder — field names now match LeadReminder model
            'reminder'                  => ['nullable', 'array'],
            'reminder.remind_at'        => ['required_with:reminder', 'date', 'after:now'],
            'reminder.title'            => ['required_with:reminder', 'string', 'max:255'],
            'reminder.description'      => ['nullable', 'string', 'max:1000'],
            'reminder.type'             => ['nullable', 'string', 'in:' . implode(',', array_keys(LeadReminder::TYPES))],
            'reminder.priority'         => ['nullable', 'string', 'in:' . implode(',', array_keys(Lead::PRIORITIES))],
        ]);

        $reminderCreated = false;
        $lead = null;

        DB::transaction(function () use ($validated, $request, &$lead, &$reminderCreated) {
            $leadData = collect($validated)->except('reminder')->toArray();
            $leadData['created_by'] = $request->user()->id;

            $lead = Lead::create($leadData);

            if (!empty($validated['reminder'])) {
                LeadReminder::create([
                    'lead_id'      => $lead->id,
                    'user_id'      => $request->user()->id,
                    'remind_at'    => $validated['reminder']['remind_at'],
                    'title'        => $validated['reminder']['title'],
                    'description'  => $validated['reminder']['description'] ?? null,
                    'type'         => $validated['reminder']['type'] ?? 'follow_up',
                    'priority'     => $validated['reminder']['priority'] ?? 'medium',
                    'is_completed' => false,
                ]);
                $reminderCreated = true;
            }
        });

        $lead->load(['branch:id,name', 'assignedTo:id,name', 'createdBy:id,name', 'reminders']);

        return response()->json([
            'status'           => true,
            'message'          => 'Lead created successfully.',
            'data'             => $this->formatLeadDetail($lead),
            'reminder_created' => $reminderCreated,
        ], 201);
    }

    // =========================================================================
    // SHOW — Single lead details
    // =========================================================================

    #[OA\Get(
        path: "/api/mobile/leads/{id}",
        summary: "Get a single lead",
        description: "Returns full details of a lead including branch, assigned user, call updates, reminders, products, and quotations.",
        security: [["sanctum" => []]],
        tags: ["Leads"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Lead ID", schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lead detail",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "status", type: "boolean", example: true),
                    new OA\Property(property: "data",   ref: "#/components/schemas/LeadDetail"),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/UnauthenticatedResponse")),
            new OA\Response(response: 404, description: "Lead not found",   content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
        ]
    )]
    public function show(Lead $lead): JsonResponse
    {
        $lead->load([
            'branch:id,name',
            'assignedTo:id,name',
            'createdBy:id,name',
            'callUpdates.user:id,name',
            'reminders.user:id,name',
            'products',
            'quotations.items',
            'quotations.createdBy:id,name',
        ]);

        return response()->json([
            'status' => true,
            'data'   => $this->formatLeadDetail($lead),
        ]);
    }

    // =========================================================================
    // UPDATE — Full update
    // =========================================================================

    #[OA\Put(
        path: "/api/mobile/leads/{id}",
        summary: "Update a lead",
        description: "Updates all fields of an existing lead.",
        security: [["sanctum" => []]],
        tags: ["Leads"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Lead ID", schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "company_name",  type: "string",  example: "Acme Pvt Ltd"),
                    new OA\Property(property: "contact_name",  type: "string",  example: "Ravi Kumar"),
                    new OA\Property(property: "lead_date",     type: "string",  format: "date", nullable: true),
                    new OA\Property(property: "mobile_number", type: "string",  example: "9876543210"),
                    new OA\Property(property: "email",         type: "string",  format: "email", nullable: true),
                    new OA\Property(property: "lead_source",   type: "string",  enum: ["reference","ad_campaign","direct_visit","invitation","cold_outreach","social_media","website"]),
                    new OA\Property(property: "lead_status",   type: "string",  enum: ["new","qualified","proposal","negotiation","won","lost"]),
                    new OA\Property(property: "product_name",  type: "string",  nullable: true),
                    new OA\Property(property: "priority",      type: "string",  enum: ["low","medium","high"]),
                    new OA\Property(property: "deal_value",    type: "number",  format: "float", nullable: true),
                    new OA\Property(property: "remarks",       type: "string",  nullable: true),
                    new OA\Property(property: "branch_id",     type: "integer", nullable: true),
                    new OA\Property(property: "assigned_to",   type: "integer", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Lead updated",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "status",  type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string",  example: "Lead updated successfully."),
                    new OA\Property(property: "data",    ref: "#/components/schemas/LeadDetail"),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/UnauthenticatedResponse")),
            new OA\Response(response: 404, description: "Lead not found",   content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 422, description: "Validation error",  content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")),
        ]
    )]
    public function update(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'company_name'  => ['sometimes', 'required', 'string', 'max:255'],
            'contact_name'  => ['sometimes', 'required', 'string', 'max:255'],
            'lead_date'     => ['nullable', 'date'],
            'mobile_number' => ['sometimes', 'required', 'string', 'max:20'],
            'email'         => ['nullable', 'email', 'max:255'],
            'lead_source'   => ['sometimes', 'required', 'string', 'in:' . implode(',', array_keys(Lead::SOURCES))],
            'lead_status'   => ['sometimes', 'required', 'string', 'in:' . implode(',', array_keys(Lead::STATUSES))],
            'product_name'  => ['nullable', 'string', 'max:255'],
            'priority'      => ['sometimes', 'required', 'string', 'in:' . implode(',', array_keys(Lead::PRIORITIES))],
            'deal_value'    => ['nullable', 'numeric', 'min:0'],
            'remarks'       => ['nullable', 'string'],
            'branch_id'     => ['nullable', 'integer', 'exists:branches,id'],
            'assigned_to'   => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $lead->update($validated);
        $lead->load(['branch:id,name', 'assignedTo:id,name', 'createdBy:id,name', 'reminders']);

        return response()->json([
            'status'  => true,
            'message' => 'Lead updated successfully.',
            'data'    => $this->formatLeadDetail($lead),
        ]);
    }

    // =========================================================================
    // DESTROY — Soft delete
    // =========================================================================

    #[OA\Delete(
        path: "/api/mobile/leads/{id}",
        summary: "Delete a lead",
        description: "Soft-deletes a lead. The record is retained in the database but excluded from all queries.",
        security: [["sanctum" => []]],
        tags: ["Leads"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Lead ID", schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lead deleted",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "status",  type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string",  example: "Lead removed successfully."),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/UnauthenticatedResponse")),
            new OA\Response(response: 404, description: "Lead not found",   content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
        ]
    )]
    public function destroy(Lead $lead): JsonResponse
    {
        $name = $lead->company_name;
        $lead->delete();

        return response()->json([
            'status'  => true,
            'message' => "Lead \"{$name}\" removed successfully.",
        ]);
    }

    // =========================================================================
    // UPDATE STATUS — Quick patch
    // =========================================================================

    #[OA\Patch(
        path: "/api/mobile/leads/{id}/status",
        summary: "Quick status update",
        description: "Updates only the lead_status field. Useful for kanban-style drag-and-drop or quick status changes.",
        security: [["sanctum" => []]],
        tags: ["Leads"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Lead ID", schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["lead_status"],
                properties: [
                    new OA\Property(property: "lead_status", type: "string", enum: ["new","qualified","proposal","negotiation","won","lost"], example: "won"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Status updated",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "status",       type: "boolean", example: true),
                    new OA\Property(property: "message",      type: "string",  example: "Lead status updated to Won."),
                    new OA\Property(property: "lead_status",  type: "string",  example: "won"),
                    new OA\Property(property: "status_label", type: "string",  example: "Won"),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/UnauthenticatedResponse")),
            new OA\Response(response: 404, description: "Lead not found",   content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 422, description: "Validation error",  content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")),
        ]
    )]
    public function updateStatus(Request $request, Lead $lead): JsonResponse
    {
        $request->validate([
            'lead_status' => ['required', 'string', 'in:' . implode(',', array_keys(Lead::STATUSES))],
        ]);

        $lead->update(['lead_status' => $request->lead_status]);

        return response()->json([
            'status'       => true,
            'message'      => "Lead status updated to {$lead->status_label}.",
            'lead_status'  => $lead->lead_status,
            'status_label' => $lead->status_label,
        ]);
    }

    // =========================================================================
    // META — Enums / filter options
    // =========================================================================

    #[OA\Get(
        path: "/api/mobile/leads/meta",
        summary: "Get lead filter options (enums, branches, users, products)",
        description: "Returns all enum values, active branches, active users, and known product names. Use this to populate filter dropdowns and create/edit forms.",
        security: [["sanctum" => []]],
        tags: ["Leads"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Meta data for leads",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "status", type: "boolean", example: true),
                    new OA\Property(property: "data",   type: "object", properties: [
                        new OA\Property(property: "sources",          type: "object", example: ["reference" => "Reference"]),
                        new OA\Property(property: "statuses",         type: "object", example: ["new" => "New"]),
                        new OA\Property(property: "priorities",       type: "object", example: ["low" => "Low"]),
                        new OA\Property(property: "reminder_types",   type: "object", example: ["call" => "Call"]),
                        new OA\Property(property: "branches",   type: "array",  items: new OA\Items(properties: [new OA\Property(property: "id", type: "integer"), new OA\Property(property: "name", type: "string")])),
                        new OA\Property(property: "users",      type: "array",  items: new OA\Items(properties: [new OA\Property(property: "id", type: "integer"), new OA\Property(property: "name", type: "string")])),
                        new OA\Property(property: "products",   type: "array",  items: new OA\Items(type: "string")),
                    ]),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/UnauthenticatedResponse")),
        ]
    )]
    public function meta(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => [
                'sources'        => Lead::SOURCES,
                'statuses'       => Lead::STATUSES,
                'priorities'     => Lead::PRIORITIES,
                'reminder_types' => LeadReminder::TYPES,
                'branches'       => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
                'users'          => User::where('is_active', true)->orderBy('name')->get(['id', 'name']),
                'products'       => Lead::select('product_name')->whereNotNull('product_name')->distinct()->orderBy('product_name')->pluck('product_name'),
            ],
        ]);
    }

    // =========================================================================
    // PRIVATE FORMATTERS
    // =========================================================================

    private function formatLeadSummary(Lead $lead): array
    {
        return [
            'id'                   => $lead->id,
            'company_name'         => $lead->company_name,
            'contact_name'         => $lead->contact_name,
            'mobile_number'        => $lead->mobile_number,
            'email'                => $lead->email,
            'lead_date'            => $lead->lead_date?->toDateString(),
            'lead_source'          => $lead->lead_source,
            'source_label'         => $lead->source_label,
            'lead_status'          => $lead->lead_status,
            'status_label'         => $lead->status_label,
            'status_color'         => $lead->status_color,
            'priority'             => $lead->priority,
            'priority_label'       => $lead->priority_label,
            'priority_color'       => $lead->priority_color,
            'product_name'         => $lead->product_name,
            'deal_value'           => $lead->deal_value,
            'formatted_deal_value' => $lead->formatted_deal_value,
            'branch'               => $lead->branch    ? ['id' => $lead->branch->id,    'name' => $lead->branch->name]    : null,
            'assigned_to'          => $lead->assignedTo ? ['id' => $lead->assignedTo->id, 'name' => $lead->assignedTo->name] : null,
            'created_at'           => $lead->created_at?->toIso8601String(),
        ];
    }

    private function formatLeadDetail(Lead $lead): array
    {
        $base = $this->formatLeadSummary($lead);

        return array_merge($base, [
            'remarks'    => $lead->remarks,
            'created_by' => $lead->createdBy ? ['id' => $lead->createdBy->id, 'name' => $lead->createdBy->name] : null,
            'updated_at' => $lead->updated_at?->toIso8601String(),

            'call_updates' => $lead->relationLoaded('callUpdates')
                ? $lead->callUpdates->map(fn($c) => [
                    'id'        => $c->id,
                    'note'      => $c->note,
                    'called_at' => $c->called_at?->toIso8601String(),
                    'user'      => $c->user ? ['id' => $c->user->id, 'name' => $c->user->name] : null,
                ])->values()
                : [],

            // FIX: use correct LeadReminder field names (is_completed, title, description, type)
            'reminders' => $lead->relationLoaded('reminders')
                ? $lead->reminders->map(fn($r) => [
                    'id'           => $r->id,
                    'title'        => $r->title,
                    'description'  => $r->description,
                    'remind_at'    => $r->remind_at?->toIso8601String(),
                    'type'         => $r->type,
                    'type_label'   => $r->type_label,
                    'type_icon'    => $r->type_icon,
                    'priority'     => $r->priority,
                    'is_completed' => (bool) $r->is_completed,
                    'is_overdue'   => $r->is_overdue,
                    'completed_at' => $r->completed_at?->toIso8601String(),
                    'user'         => $r->user ? ['id' => $r->user->id, 'name' => $r->user->name] : null,
                ])->values()
                : [],

            'products' => $lead->relationLoaded('products')
                ? $lead->products->map(fn($p) => [
                    'id'     => $p->id,
                    'name'   => $p->name,
                    'status' => $p->status,
                    'qty'    => $p->qty,
                    'price'  => $p->price,
                ])->values()
                : [],

            'quotations' => $lead->relationLoaded('quotations')
                ? $lead->quotations->map(fn($q) => [
                    'id'         => $q->id,
                    'status'     => $q->status,
                    'total'      => $q->total,
                    'created_at' => $q->created_at?->toIso8601String(),
                    'created_by' => $q->createdBy ? ['id' => $q->createdBy->id, 'name' => $q->createdBy->name] : null,
                    'items'      => $q->items ?? [],
                ])->values()
                : [],
        ]);
    }
}


// =============================================================================
// SWAGGER SCHEMA DEFINITIONS
// =============================================================================

#[OA\Schema(
    schema: "LeadSummary",
    description: "Compact lead representation used in list views",
    properties: [
        new OA\Property(property: "id",            type: "integer", example: 42),
        new OA\Property(property: "company_name",  type: "string",  example: "Acme Pvt Ltd"),
        new OA\Property(property: "contact_name",  type: "string",  example: "Ravi Kumar"),
        new OA\Property(property: "mobile_number", type: "string",  example: "9876543210"),
        new OA\Property(property: "email",         type: "string",  nullable: true, example: "ravi@acme.com"),
        new OA\Property(property: "lead_date",     type: "string",  format: "date", example: "2025-07-15"),
        new OA\Property(property: "lead_source",   type: "string",  example: "reference"),
        new OA\Property(property: "source_label",  type: "string",  example: "Reference"),
        new OA\Property(property: "lead_status",   type: "string",  example: "new"),
        new OA\Property(property: "status_label",  type: "string",  example: "New"),
        new OA\Property(property: "status_color",  type: "object",  properties: [
            new OA\Property(property: "bg",     type: "string", example: "#eff6ff"),
            new OA\Property(property: "text",   type: "string", example: "#2563eb"),
            new OA\Property(property: "border", type: "string", example: "#bfdbfe"),
        ]),
        new OA\Property(property: "priority",       type: "string",  example: "high"),
        new OA\Property(property: "priority_label", type: "string",  example: "High"),
        new OA\Property(property: "priority_color", type: "object"),
        new OA\Property(property: "product_name",   type: "string",  nullable: true, example: "Solar Panel 10kW"),
        new OA\Property(property: "deal_value",     type: "number",  nullable: true, example: 250000.00),
        new OA\Property(property: "formatted_deal_value", type: "string", example: "₹2,50,000.00"),
        new OA\Property(property: "branch",      type: "object", nullable: true, properties: [new OA\Property(property: "id", type: "integer"), new OA\Property(property: "name", type: "string")]),
        new OA\Property(property: "assigned_to", type: "object", nullable: true, properties: [new OA\Property(property: "id", type: "integer"), new OA\Property(property: "name", type: "string")]),
        new OA\Property(property: "created_at",  type: "string",  format: "date-time"),
    ]
)]
#[OA\Schema(
    schema: "LeadDetail",
    description: "Full lead record with all related data",
    allOf: [
        new OA\Schema(ref: "#/components/schemas/LeadSummary"),
        new OA\Schema(properties: [
            new OA\Property(property: "remarks",    type: "string",  nullable: true),
            new OA\Property(property: "created_by", type: "object",  nullable: true, properties: [new OA\Property(property: "id", type: "integer"), new OA\Property(property: "name", type: "string")]),
            new OA\Property(property: "updated_at", type: "string",  format: "date-time"),
            new OA\Property(property: "call_updates", type: "array", items: new OA\Items(properties: [
                new OA\Property(property: "id",        type: "integer"),
                new OA\Property(property: "note",      type: "string"),
                new OA\Property(property: "called_at", type: "string", format: "date-time"),
                new OA\Property(property: "user",      type: "object", nullable: true, properties: [new OA\Property(property: "id", type: "integer"), new OA\Property(property: "name", type: "string")]),
            ])),
            new OA\Property(property: "reminders", type: "array", items: new OA\Items(properties: [
                new OA\Property(property: "id",           type: "integer"),
                new OA\Property(property: "title",        type: "string"),
                new OA\Property(property: "description",  type: "string",  nullable: true),
                new OA\Property(property: "remind_at",    type: "string",  format: "date-time"),
                new OA\Property(property: "type",         type: "string",  example: "call"),
                new OA\Property(property: "type_label",   type: "string",  example: "Call"),
                new OA\Property(property: "type_icon",    type: "string",  example: "📞"),
                new OA\Property(property: "priority",     type: "string",  example: "medium"),
                new OA\Property(property: "is_completed", type: "boolean"),
                new OA\Property(property: "is_overdue",   type: "boolean"),
                new OA\Property(property: "completed_at", type: "string",  format: "date-time", nullable: true),
                new OA\Property(property: "user",         type: "object",  nullable: true, properties: [new OA\Property(property: "id", type: "integer"), new OA\Property(property: "name", type: "string")]),
            ])),
            new OA\Property(property: "products",   type: "array", items: new OA\Items(type: "object")),
            new OA\Property(property: "quotations", type: "array", items: new OA\Items(type: "object")),
        ])
    ]
)]
#[OA\Schema(
    schema: "LeadStats",
    description: "Aggregate stats shown on the leads dashboard",
    properties: [
        new OA\Property(property: "total",         type: "integer", example: 120),
        new OA\Property(property: "new",           type: "integer", example: 34),
        new OA\Property(property: "won",           type: "integer", example: 18),
        new OA\Property(property: "lost",          type: "integer", example: 7),
        new OA\Property(property: "pipeline",      type: "number",  example: 1250000.00, description: "Sum of deal_value for non-won/lost leads"),
        new OA\Property(property: "high_priority", type: "integer", example: 12),
    ]
)]
class LeadSchemas {}