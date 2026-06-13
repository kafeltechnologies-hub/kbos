<?php

use Livewire\Component;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;

new class extends Component
{
    public $departments = [];
    public $companies = [];
    public $branches = [];

    public $company_id;
    public $branch_id;
    public $name;
    public $description;
    public $active = true;

    public function mount()
    {
        $this->companies = Company::where('active', true)->orderBy('name')->get();
        $this->loadDepartments();
    }

    public function rendering($view)
    {
        $view->layout('layouts.erp');
    }

    public function updatedCompanyId()
    {
        $this->branch_id = null;

        $this->branches = Branch::where('company_id', $this->company_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }

    public function loadDepartments()
    {
        $this->departments = Department::with(['company', 'branch'])
            ->latest()
            ->get();
    }

    public function generateDepartmentCode()
    {
        $lastDepartment = Department::orderBy('id', 'desc')->first();
        $nextNumber = $lastDepartment ? $lastDepartment->id + 1 : 1;

        return 'DEP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function save()
    {
        $this->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required',
        ]);

        Department::create([
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'code' => $this->generateDepartmentCode(),
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
        ]);

        $this->reset([
            'company_id',
            'branch_id',
            'name',
            'description',
        ]);

        $this->branches = [];
        $this->active = true;

        $this->loadDepartments();

        session()->flash('success', 'Department created successfully.');
    }
};
?>

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold">Departments</h1>
        <p class="text-gray-600 mt-1">Manage business departments under branches.</p>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Create Department</h2>

        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium">Company *</label>

                    <select wire:model.live="company_id" class="mt-1 w-full rounded border-gray-300">
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
                    <label class="block text-sm font-medium">Branch</label>

                    <select wire:model="branch_id" class="mt-1 w-full rounded border-gray-300">
                        <option value="">Select branch</option>

                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('branch_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Department Name *</label>

                    <input
                        wire:model="name"
                        type="text"
                        class="mt-1 w-full rounded border-gray-300"
                    >

                    @error('name')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Description</label>

                    <textarea
                        wire:model="description"
                        rows="3"
                        class="mt-1 w-full rounded border-gray-300"
                    ></textarea>
                </div>

            </div>

            <div class="mt-6">
                <button
                    type="submit"
                    class="bg-slate-900 text-white px-4 py-2 rounded hover:bg-slate-800"
                >
                    Save Department
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Department List</h2>
            <span class="text-sm text-gray-500">{{ count($departments) }} Departments</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Code</th>
                        <th class="text-left py-2">Department</th>
                        <th class="text-left py-2">Company</th>
                        <th class="text-left py-2">Branch</th>
                        <th class="text-left py-2">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($departments as $department)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3">{{ $department->code }}</td>
                            <td class="py-3">{{ $department->name }}</td>
                            <td class="py-3">{{ $department->company?->name }}</td>
                            <td class="py-3">{{ $department->branch?->name }}</td>
                            <td class="py-3">
                                @if($department->active)
                                    <span class="text-green-600 font-medium">Active</span>
                                @else
                                    <span class="text-red-600 font-medium">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">
                                No departments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>