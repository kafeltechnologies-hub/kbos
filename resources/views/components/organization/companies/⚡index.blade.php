<?php

use Livewire\Component;
use App\Models\Company;

new class extends Component
{
    // State Properties
    public array $companies = [];
    public string $search = '';

    // Form Fields
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

    // Lookups
    public array $countries = [
        'Ghana', 'Nigeria', 'Togo', 'Benin', 'Burkina Faso',
        'Ivory Coast', 'Liberia', 'Sierra Leone', 'Senegal',
        'South Africa', 'United Kingdom', 'United States',
        'Canada', 'Germany', 'China',
    ];
    
    
    public function mount(): void
    {
        $this->loadCompanies();
    }

    public function rendering($view): void
    {
        $view->layout('layouts.erp');
    }

    public function updatedSearch(): void
    {
        $this->loadCompanies();
    }

    public function loadCompanies(): void
    {
        $this->companies = Company::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('tin', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->get()
            ->toArray();
    }

    public function generateCompanyCode(): string
    {
        $lastCompany = Company::orderBy('id', 'desc')->first();
        $nextNumber = $lastCompany ? $lastCompany->id + 1 : 1;

        return 'CMP' . str_pad((string)$nextNumber, 4, '0', STR_PAD_LEFT);
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

    public function refreshRecords(): void
    {
        $this->loadCompanies();
    }

    public function save(): void
    {
        $this->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        Company::create([
            'code'                => $this->generateCompanyCode(),
            'name'                => $this->name,
            'registration_number' => $this->registration_number,
            'tin'                 => $this->tin,
            'vat_number'          => $this->vat_number,
            'ssnit_number'        => $this->ssnit_number,
            'email'               => $this->email,
            'phone'               => $this->phone,
            'address'             => $this->address,
            'country'             => $this->country,
            'active'              => $this->active,
        ]);

        $this->clearForm();
        $this->loadCompanies();

        session()->flash('success', 'Company profile written to ledger successfully.');
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
};
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/geist@1.3.0/dist/font/sans.css">

<style>
    .font-erp-clean { font-family: 'Geist Sans', system-ui, -apple-system, sans-serif; }
</style>

<div class="min-h-screen bg-slate-100 text-slate-900 font-erp-clean p-6">

    <form id="company-form" wire:submit="save">

        <div class="border border-slate-300 bg-white shadow-sm rounded-none overflow-hidden">
            
            <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-4 py-3 flex items-center justify-between border-b border-slate-900">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase font-mono block">Organization Area</span>
                    <h1 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0V11m0 0h4m-4 0H5m12 0h2" /></svg>
                        Master Data — Corporate Registries
                    </h1>
                </div>
                <div class="flex items-center gap-6 text-right">
                    <div class="hidden sm:block border-l border-slate-600 pl-4">
                        <span class="text-[10px] block uppercase font-mono text-slate-400">Ledger Count</span>
                        <span class="text-base font-black font-mono text-white">{{ count($companies) }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 bg-slate-50 px-3 py-2">
                <button type="button" wire:click.prevent="createNew"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 hover:text-blue-700 active:bg-slate-200 transition rounded-none shadow-sm">
                        Create New
                    </button>

                    <button type="button" wire:click="postLedger"
                        class="inline-flex items-center gap-1 px-4 py-1.5 text-xs font-semibold text-white bg-blue-700 border border-blue-800 hover:bg-blue-800 active:bg-blue-900 transition rounded-none shadow-sm">
                        Post Ledger (Save)
                    </button>

                    <button type="button" wire:click="clearBuffer"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 hover:text-slate-800 active:bg-slate-200 transition rounded-none shadow-sm">
                        Clear Buffer
                    </button>

                    <button type="button" wire:click="sync"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 hover:text-slate-800 active:bg-slate-200 transition rounded-none shadow-sm">
                        Sync
                    </button>
                <div class="ml-auto px-3 py-1 bg-slate-200 border border-slate-300 text-[11px] font-mono font-bold text-slate-700 rounded-none">
                    SYSTEM GENERATED ID: <span class="text-blue-700">{{ $this->generateCompanyCode() }}</span>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mt-4 border-l-4 border-green-600 bg-green-50 p-3 text-xs font-medium text-green-900 flex items-center gap-2 rounded-none shadow-sm">
                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('info'))
            <div class="mt-4 border-l-4 border-blue-600 bg-blue-50 p-3 text-xs font-medium text-blue-900 flex items-center gap-2 rounded-none shadow-sm">
                {{ session('info') }}
            </div>
        @endif

        <div class="mt-4 border border-slate-300 bg-white rounded-none shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">01. General Profile Block</h2>
            </div>
            
            <div class="p-6 bg-slate-50/20" style="display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; column-gap: 2.5rem !important; row-gap: 1.25rem !important;">
                
                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; width: 100% !important;">
                    <label class="text-xs font-bold text-slate-600" style="width: 160px !important; flex-shrink: 0 !important; text-align: right !important; padding-right: 1rem !important;">Company Legal Name <span class="text-red-500">*</span></label>
                    <div style="flex: 1 1 0% !important; min-width: 0 !important;">
                        <input wire:model="name" type="text"
                            class="w-full text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                        @error('name') <p class="mt-1 text-[11px] font-semibold text-red-600 absolute whitespace-nowrap">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; width: 100% !important;">
                    <label class="text-xs font-bold text-slate-600" style="width: 160px !important; flex-shrink: 0 !important; text-align: right !important; padding-right: 1rem !important;">Country Zone</label>
                    <select wire:model="country"
                        class="text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition"
                        style="flex: 1 1 0% !important; min-width: 0 !important;">
                        @foreach($countries as $countryName)
                            <option value="{{ $countryName }}">{{ $countryName }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; width: 100% !important;">
                    <label class="text-xs font-bold text-slate-600" style="width: 160px !important; flex-shrink: 0 !important; text-align: right !important; padding-right: 1rem !important;">Registration ID / No.</label>
                    <input wire:model="registration_number" type="text"
                        class="text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition"
                        style="flex: 1 1 0% !important; min-width: 0 !important;">
                </div>

                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; width: 100% !important;">
                    <label class="text-xs font-bold text-slate-600" style="width: 160px !important; flex-shrink: 0 !important; text-align: right !important; padding-right: 1rem !important;">Record State Status</label>
                    <select wire:model="active"
                        class="text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition"
                        style="flex: 1 1 0% !important; min-width: 0 !important;">
                        <option value="1">Active Operational Ledger</option>
                        <option value="0">Inactive / Suspended</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="mt-6 border border-slate-300 bg-white rounded-none shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">02. Statutory & Revenue Authority Ledger</h2>
            </div>
            
            <div class="p-6 bg-slate-50/20" style="display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; column-gap: 2.5rem !important; row-gap: 1.25rem !important;">
                
                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; width: 100% !important;">
                    <label class="text-xs font-bold text-slate-600" style="width: 160px !important; flex-shrink: 0 !important; text-align: right !important; padding-right: 1rem !important;">Tax Identification (TIN)</label>
                    <input wire:model="tin" type="text"
                        class="text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition"
                        style="flex: 1 1 0% !important; min-width: 0 !important;">
                </div>

                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; width: 100% !important;">
                    <label class="text-xs font-bold text-slate-600" style="width: 160px !important; flex-shrink: 0 !important; text-align: right !important; padding-right: 1rem !important;">VAT Registration Value</label>
                    <input wire:model="vat_number" type="text"
                        class="text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition"
                        style="flex: 1 1 0% !important; min-width: 0 !important;">
                </div>

                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; width: 100% !important;">
                    <label class="text-xs font-bold text-slate-600" style="width: 160px !important; flex-shrink: 0 !important; text-align: right !important; padding-right: 1rem !important;">SSNIT Identifier</label>
                    <input wire:model="ssnit_number" type="text"
                        class="text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition"
                        style="flex: 1 1 0% !important; min-width: 0 !important;">
                </div>

                <div></div>

            </div>
        </div>

        <div class="mt-6 border border-slate-300 bg-white rounded-none shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">03. Communications & Address Node</h2>
            </div>
            
            <div class="p-6 bg-slate-50/20" style="display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; column-gap: 2.5rem !important; row-gap: 1.25rem !important;">
                
                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; width: 100% !important;">
                    <label class="text-xs font-bold text-slate-600" style="width: 160px !important; flex-shrink: 0 !important; text-align: right !important; padding-right: 1rem !important;">Primary Contact Email</label>
                    <div style="flex: 1 1 0% !important; min-width: 0 !important;">
                        <input wire:model="email" type="email"
                            class="w-full text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                        @error('email') <p class="mt-1 text-[11px] font-semibold text-red-600 absolute whitespace-nowrap">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; width: 100% !important;">
                    <label class="text-xs font-bold text-slate-600" style="width: 160px !important; flex-shrink: 0 !important; text-align: right !important; padding-right: 1rem !important;">Primary Office Phone</label>
                    <input wire:model="phone" type="text"
                        class="text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition"
                        style="flex: 1 1 0% !important; min-width: 0 !important;">
                </div>

                <div style="display: flex !important; flex-direction: row !important; align-items: flex-start !important; width: 100% !important;">
                    <label class="text-xs font-bold text-slate-600" style="width: 160px !important; flex-shrink: 0 !important; text-align: right !important; padding-right: 1rem !important; padding-top: 0.375rem !important;">Registered Address</label>
                    <textarea wire:model="address" rows="2"
                        class="text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition resize-none"
                        style="flex: 1 1 0% !important; min-width: 0 !important;"></textarea>
                </div>

                <div></div>

            </div>
        </div>

    </form>

    <div class="mt-6 border border-slate-300 bg-white shadow-sm rounded-none overflow-hidden">
        
        <div class="bg-slate-800 px-4 py-3 border-b border-slate-900">
            <h2 class="text-xs font-bold uppercase tracking-wider text-white">Database Registry Ledger Outputs</h2>
        </div>

        <div class="w-full bg-slate-200 border-b border-slate-300 flex items-center shadow-inner">
            <span class="pl-4 text-slate-500 font-mono text-sm select-none">🔍</span>
            <input wire:model.live="search" type="text"
                placeholder="Filter records inline..."
                class="w-full bg-transparent border-0 px-3 py-3 text-xs text-slate-900 placeholder-slate-500 focus:ring-0 outline-none font-medium">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap table-fixed">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300 select-none">
                    <tr>
                        <th class="w-24 px-4 py-4 border-r border-slate-200">System Code</th>
                        <th class="w-56 px-4 py-4 border-r border-slate-200">Corporate Designation Name</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Tax TIN</th>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Telephone Line</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Country Origin</th>
                        <th class="w-24 px-4 py-4">State</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($companies as $company)
                        <tr class="hover:bg-blue-50/70 border-b border-slate-200 transition">
                            <td class="px-4 py-6 font-mono font-bold text-blue-800 border-r border-slate-200 bg-slate-50/50">
                                {{ $company['code'] ?? '-' }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200 overflow-hidden text-ellipsis">
                                <div class="font-bold text-slate-900 truncate">{{ $company['name'] ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5">
                                    {{ $company['email'] ?: 'NULL_PTR' }}
                                </div>
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono border-r border-slate-200">{{ $company['tin'] ?: '-' }}</td>
                            <td class="px-4 py-6 text-slate-600 border-r border-slate-200">{{ $company['phone'] ?: '-' }}</td>
                            <td class="px-4 py-6 text-slate-600 border-r border-slate-200">{{ $company['country'] ?? '-' }}</td>

                            <td class="px-4 py-6">
                                @if($company['active'] ?? false)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-none text-[10px] font-bold uppercase tracking-wide bg-green-100 text-green-800 border border-green-300">
                                        ACTIVE
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-none text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-500 border border-slate-300">
                                        LOCKED
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                [Err] 0 records returned based on query arguments.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>