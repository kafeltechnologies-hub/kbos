<?php

namespace App\Livewire\Organization;

use App\Models\Company;
use Livewire\Component;

class CompaniesPage extends Component
{
    public array $companies = [];
    public string $search = '';

    public ?string $name = null;
    public ?string $registration_number = null;
    public ?string $tin = null;
    public ?string $vat_number = null;
    public ?string $ssnit_number = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $address = null;

    public string $country = 'Ghana';
    public bool $active = true;

    public array $countries = [
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

    public function mount(): void
    {
        $this->loadCompanies();
    }

    public function updatedSearch(): void
    {
        $this->loadCompanies();
    }

    public function loadCompanies(): void
    {
        $this->companies = Company::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('tin', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->get()
            ->toArray();
    }

    public function createNew(): void
    {
        $this->clearForm();

        session()->flash('info', 'New company entry buffer initialized.');
    }

    public function postLedger(): void
    {
        $this->save();
    }

    public function clearBuffer(): void
    {
        $this->clearForm();

        session()->flash('info', 'Entry buffer cleared successfully.');
    }

    public function sync(): void
    {
        $this->loadCompanies();

        session()->flash('info', 'Registry ledger synchronized successfully.');
    }

    public function generateCompanyCode(): string
    {
        $lastCompany = Company::latest('id')->first();

        $nextNumber = $lastCompany
            ? $lastCompany->id + 1
            : 1;

        return 'CMP' . str_pad(
            (string) $nextNumber,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    public function clearForm(): void
    {
        $this->reset([
            'name',
            'registration_number',
            'tin',
            'vat_number',
            'ssnit_number',
            'email',
            'phone',
            'address',
        ]);

        $this->country = 'Ghana';
        $this->active = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
        ]);

        Company::create([
            'code' => $this->generateCompanyCode(),
            'name' => $this->name,
            'registration_number' => $this->registration_number,
            'tin' => $this->tin,
            'vat_number' => $this->vat_number,
            'ssnit_number' => $this->ssnit_number,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'country' => $this->country,
            'active' => $this->active,
        ]);

        $this->clearForm();
        $this->loadCompanies();

        session()->flash(
            'success',
            'Company profile written to ledger successfully.'
        );
    }

    public function render()
    {
        return view('livewire.organization.companies-page')
            ->layout('layouts.erp');
    }
}