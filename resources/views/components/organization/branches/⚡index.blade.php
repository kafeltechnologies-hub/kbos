<?php

use Livewire\Component;
use App\Models\Branch;
use App\Models\Company;

new class extends Component
{
    public $branches = [];
    public $companies = [];

    public $company_id;
    public $name;
    public $region;
    public $phone;
    public $address;
    public $active = true;

    public function mount()
    {
        $this->companies = Company::where('active', true)->orderBy('name')->get();
        $this->loadBranches();
    }

    public function rendering($view)
    {
        $view->layout('layouts.erp');
    }

    public function loadBranches()
    {
        $this->branches = Branch::with('company')->latest()->get();
    }

    public function generateBranchCode()
    {
        $lastBranch = Branch::orderBy('id', 'desc')->first();
        $nextNumber = $lastBranch ? $lastBranch->id + 1 : 1;

        return 'BR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function save()
    {
        $this->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required',
        ]);

        Branch::create([
            'company_id' => $this->company_id,
            'code' => $this->generateBranchCode(),
            'name' => $this->name,
            'region' => $this->region,
            'phone' => $this->phone,
            'address' => $this->address,
            'active' => $this->active,
        ]);

        $this->reset([
            'company_id',
            'name',
            'region',
            'phone',
            'address',
        ]);

        $this->active = true;
        $this->loadBranches();

        session()->flash('success', 'Branch created successfully.');
    }
};
?>

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold">Branches</h1>
        <p class="text-gray-600 mt-1">Manage company branches and operating locations.</p>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Create Branch</h2>

        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium">Company *</label>
                    <select wire:model="company_id" class="mt-1 w-full rounded border-gray-300">
                        <option value="">Select company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Branch Name *</label>
                    <input wire:model="name" type="text" class="mt-1 w-full rounded border-gray-300">
                    @error('name')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Region</label>
                    <input wire:model="region" type="text" class="mt-1 w-full rounded border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium">Phone</label>
                    <input wire:model="phone" type="text" class="mt-1 w-full rounded border-gray-300">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Address</label>
                    <textarea wire:model="address" rows="3" class="mt-1 w-full rounded border-gray-300"></textarea>
                </div>

            </div>

            <div class="mt-6">
                <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded hover:bg-slate-800">
                    Save Branch
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Branch List</h2>
            <span class="text-sm text-gray-500">{{ count($branches) }} Branches</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Code</th>
                        <th class="text-left py-2">Branch</th>
                        <th class="text-left py-2">Company</th>
                        <th class="text-left py-2">Region</th>
                        <th class="text-left py-2">Phone</th>
                        <th class="text-left py-2">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($branches as $branch)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3">{{ $branch->code }}</td>
                            <td class="py-3">{{ $branch->name }}</td>
                            <td class="py-3">{{ $branch->company?->name }}</td>
                            <td class="py-3">{{ $branch->region }}</td>
                            <td class="py-3">{{ $branch->phone }}</td>
                            <td class="py-3">
                                @if($branch->active)
                                    <span class="text-green-600 font-medium">Active</span>
                                @else
                                    <span class="text-red-600 font-medium">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">
                                No branches found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>