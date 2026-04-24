<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::query()
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('company_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('mobile_number', 'like', '%' . $search . '%');
                });
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('company_status', $request->status);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('pages.company.index', compact('companies'));
    }

    public function create()
    {
        return view('pages.company.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->validated());

        return redirect()
            ->route('companies.index')
            ->with('success', "Company <strong>{$company->company_name}</strong> created successfully.");
    }

    public function show(Company $company)
    {
        return view('pages.company.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('pages.company.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());

        return redirect()
            ->route('companies.show', $company)
            ->with('success', "Company <strong>{$company->company_name}</strong> updated successfully.");
    }

    public function destroy(Company $company)
    {
        $companyName = $company->company_name;
        $company->delete();

        return redirect()
            ->route('companies.index')
            ->with('success', "Company <strong>{$companyName}</strong> deleted successfully.");
    }
}
