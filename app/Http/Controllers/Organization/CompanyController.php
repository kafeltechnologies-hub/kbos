<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display company ledger
     */
    public function index()
    {
        $companies = Company::orderBy('name')->get();

        return view('organization.companies.index', compact('companies'));
    }

    /**
     * Show create company form
     */
    public function create()
    {
        $countries = [
            'Ghana',
            'Nigeria',
            'Togo',
            'Benin',
            'Burkina Faso',
            'Ivory Coast',
            'Liberia',
            'Sierra Leone',
            'Senegal',
            'South Africa',
            'United Kingdom',
            'United States',
            'Canada',
            'Germany',
            'China',
        ];

        return view(
            'organization.companies.create',
            compact('countries')
        );
    }

    /**
     * Save company
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'nullable|email',
        ]);

        Company::create([
            'code' => $this->generateCode(),
            'name' => $request->name,
            'registration_number' => $request->registration_number,
            'tin' => $request->tin,
            'vat_number' => $request->vat_number,
            'ssnit_number' => $request->ssnit_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'country' => $request->country,
            'active' => $request->active ? true : false,
        ]);

        return redirect()
            ->route('organization.companies.index')
            ->with(
                'success',
                'Company profile posted to ledger successfully.'
            );
    }

    /**
     * Refresh ledger
     */
    public function sync()
    {
        return redirect()
            ->route('organization.companies.index')
            ->with(
                'info',
                'Registry ledger synchronized successfully.'
            );
    }

    /**
     * Edit company
     */
    public function edit(Company $company)
    {
        $countries = [
            'Ghana',
            'Nigeria',
            'Togo',
            'Benin',
            'Burkina Faso',
            'Ivory Coast',
            'Liberia',
            'Sierra Leone',
            'Senegal',
            'South Africa',
            'United Kingdom',
            'United States',
            'Canada',
            'Germany',
            'China',
        ];

        return view(
            'organization.companies.edit',
            compact('company', 'countries')
        );
    }

    /**
     * Update company
     */
    public function update(
        Request $request,
        Company $company
    )
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'nullable|email',
        ]);

        $company->update([
            'name' => $request->name,
            'registration_number' => $request->registration_number,
            'tin' => $request->tin,
            'vat_number' => $request->vat_number,
            'ssnit_number' => $request->ssnit_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'country' => $request->country,
            'active' => $request->active ? true : false,
        ]);

        return redirect()
            ->route('organization.companies.index')
            ->with(
                'success',
                'Company record updated successfully.'
            );
    }

    /**
     * Delete company
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()
            ->route('organization.companies.index')
            ->with(
                'success',
                'Company removed successfully.'
            );
    }

    /**
     * Auto-generate company code
     */
    private function generateCode(): string
    {
        $last = Company::latest('id')->first();

        $next = $last ? $last->id + 1 : 1;

        return 'CMP' . str_pad(
            $next,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}