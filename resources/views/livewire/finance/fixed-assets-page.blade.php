<div class="min-h-screen bg-slate-100 p-6 text-slate-900">

    @include('livewire.finance._header', [
        'title' => 'Fixed Assets Register',
        'subtitle' => 'Create, edit, upload documents, transfer, depreciate and synchronize assets with the General Ledger.'
    ])

    @include('livewire.finance._nav')

    @if(session()->has('success'))
        <div class="mb-4 border-l-4 border-green-600 bg-green-50 p-3 text-xs font-bold text-green-900">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 border-l-4 border-red-600 bg-red-50 p-3 text-xs font-bold text-red-900">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @unless($hasFixedAssetTables)
        <div class="mb-6 border-l-4 border-red-600 bg-red-50 p-4 text-xs text-red-900">
            <p class="font-black">Missing tables</p>
            <p>fixed_assets, fixed_asset_movements or general_ledgers table is missing.</p>
        </div>
    @endunless

    {{-- KPI CARDS - HORIZONTAL --}}
    <div class="w-full mb-6 overflow-x-auto">
        <div class="flex gap-4 min-w-max">

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Total Cost</p>
                <p class="text-2xl font-black font-mono text-blue-700">{{ number_format($totalCost, 2) }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Book Value</p>
                <p class="text-2xl font-black font-mono text-green-700">{{ number_format($totalCurrentValue, 2) }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Depreciation</p>
                <p class="text-2xl font-black font-mono text-red-700">{{ number_format($totalDepreciation, 2) }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Active</p>
                <p class="text-2xl font-black font-mono text-green-700">{{ $activeAssets }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Assigned</p>
                <p class="text-2xl font-black font-mono text-amber-700">{{ $assignedAssets }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Disposed</p>
                <p class="text-2xl font-black font-mono">{{ $disposedAssets }}</p>
            </div>

        </div>
    </div>

    {{-- TABS --}}
    <div class="bg-white border border-slate-300 mb-6 overflow-x-auto">
        <div class="flex gap-2 p-3 bg-slate-100 min-w-max">
            <button type="button"
                    wire:click="$set('activeTab','register')"
                    class="px-4 py-2 text-xs font-bold border {{ $activeTab === 'register' ? 'bg-green-700 text-white border-green-800' : 'bg-white border-slate-300' }}">
                Asset Register
            </button>

            <button type="button"
                    wire:click="$set('activeTab','operations')"
                    class="px-4 py-2 text-xs font-bold border {{ $activeTab === 'operations' ? 'bg-green-700 text-white border-green-800' : 'bg-white border-slate-300' }}">
                Asset Operations
            </button>

            <button type="button"
                    wire:click="$set('activeTab','movements')"
                    class="px-4 py-2 text-xs font-bold border {{ $activeTab === 'movements' ? 'bg-green-700 text-white border-green-800' : 'bg-white border-slate-300' }}">
                Movement History
            </button>
        </div>
    </div>

    @if($activeTab === 'register')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- FORM --}}
            <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">

                <div class="bg-slate-900 text-white px-4 py-3">
                    <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Asset Definition</p>
                    <h2 class="text-sm font-black">
                        {{ $editingId ? 'Edit Fixed Asset' : 'Create Fixed Asset' }}
                    </h2>
                </div>

                <form wire:submit.prevent="saveAsset" class="p-5 space-y-4">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Asset Code</label>
                            <input type="text" wire:model="asset_code" placeholder="Auto if blank"
                                   class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Purchase Date</label>
                            <input type="date" wire:model="purchase_date"
                                   class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Asset Name</label>
                        <input type="text" wire:model="asset_name"
                               placeholder="Example: Nissan Navara"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Category</label>
                        <select wire:model="asset_category"
                                class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Serial No.</label>
                            <input type="text" wire:model="serial_number"
                                   class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Model</label>
                            <input type="text" wire:model="model"
                                   class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Manufacturer</label>
                        <input type="text" wire:model="manufacturer"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Purchase Cost</label>
                            <input type="number" step="0.01" wire:model="purchase_cost"
                                   class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Current Value</label>
                            <input type="number" step="0.01" wire:model="current_value"
                                   class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Salvage Value</label>
                            <input type="number" step="0.01" wire:model="salvage_value"
                                   class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Useful Life Years</label>
                            <input type="number" wire:model="useful_life_years"
                                   class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Depreciation Method</label>
                        <select wire:model="depreciation_method"
                                class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="straight_line">Straight Line</option>
                            <option value="reducing_balance">Reducing Balance</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Project</label>
                        <select wire:model="project_id"
                                class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">No Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">
                                    {{ $project->project_code ?? 'PRJ-'.$project->id }} — {{ $project->project_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Location</label>
                            <input type="text" wire:model="location"
                                   class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Department</label>
                            <input type="text" wire:model="department"
                                   class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Custodian</label>
                        <input type="text" wire:model="custodian"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Condition</label>
                            <select wire:model="condition"
                                    class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                                @foreach($conditions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Status</label>
                            <select wire:model="status"
                                    class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Description</label>
                        <textarea wire:model="description" rows="3"
                                  class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs resize-none"></textarea>
                    </div>

                    {{-- FILES --}}
                    <div class="border border-slate-300 bg-slate-50 p-4 space-y-4">
                        <p class="text-xs font-black uppercase text-slate-700">Asset Files</p>

                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Asset Picture</label>
                            <input type="file" wire:model="asset_photo" accept="image/*"
                                   class="mt-1 w-full border border-slate-300 bg-white px-3 py-2 text-xs">

                            <div wire:loading wire:target="asset_photo"
                                 class="mt-1 text-[10px] text-blue-700 font-bold">
                                Uploading picture...
                            </div>

                            @error('asset_photo')
                                <p class="mt-1 text-[10px] text-red-600 font-bold">{{ $message }}</p>
                            @enderror

                            @if($asset_photo)
                                <img src="{{ $asset_photo->temporaryUrl() }}"
                                     class="mt-3 h-28 w-28 object-cover border border-slate-300">
                            @elseif($existing_asset_photo_path)
                                <a href="{{ asset('storage/'.$existing_asset_photo_path) }}"
                                   target="_blank"
                                   class="block mt-2 text-[10px] text-blue-700 font-bold">
                                    View current picture
                                </a>
                            @endif
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Asset Document</label>
                            <input type="file" wire:model="asset_document"
                                   class="mt-1 w-full border border-slate-300 bg-white px-3 py-2 text-xs">

                            <div wire:loading wire:target="asset_document"
                                 class="mt-1 text-[10px] text-blue-700 font-bold">
                                Uploading document...
                            </div>

                            @error('asset_document')
                                <p class="mt-1 text-[10px] text-red-600 font-bold">{{ $message }}</p>
                            @enderror

                            @if($asset_document)
                                <p class="mt-2 text-[10px] text-green-700 font-bold">
                                    Selected: {{ $asset_document->getClientOriginalName() }}
                                </p>
                            @elseif($existing_asset_document_path)
                                <a href="{{ asset('storage/'.$existing_asset_document_path) }}"
                                   target="_blank"
                                   class="block mt-2 text-[10px] text-purple-700 font-bold">
                                    View current document
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit"
                                class="bg-green-700 text-white border border-green-800 px-4 py-2 text-xs font-bold"
                                @disabled(! $hasFixedAssetTables)>
                            {{ $editingId ? 'Update & Sync' : 'Save & Post to GL' }}
                        </button>

                        <button type="button"
                                wire:click="clearAssetForm"
                                class="bg-white border border-slate-300 px-4 py-2 text-xs font-bold">
                            Clear
                        </button>
                    </div>

                </form>
            </div>

            {{-- REGISTER --}}
            <div class="xl:col-span-2 bg-white border border-slate-300 shadow-sm overflow-hidden">

                <div class="bg-slate-900 text-white px-4 py-3">
                    <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Fixed Asset Register</p>
                    <h2 class="text-sm font-black">Assets synchronized with finance through GL postings</h2>
                </div>

                {{-- FILTERS - HORIZONTAL --}}
                <div class="bg-slate-50 border-b border-slate-300 p-4 overflow-x-auto">
                    <div class="flex gap-3 min-w-max items-center">

                        <input type="text"
                               wire:model.live.debounce.500ms="search"
                               placeholder="Search asset, serial, custodian, location..."
                               class="w-72 border border-slate-300 px-3 py-2 text-xs">

                        <select wire:model.live="filter_category"
                                class="w-52 border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>

                        <select wire:model.live="filter_status"
                                class="w-44 border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">All Status</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        <select wire:model.live="filter_condition"
                                class="w-44 border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">All Conditions</option>
                            @foreach($conditions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        <select wire:model.live="filter_project_id"
                                class="w-56 border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">
                                    {{ $project->project_name }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>

                <div class="divide-y divide-slate-200">

                    @forelse($assets as $asset)

                        @php
                            $cost = (float) ($asset->purchase_cost ?? 0);
                            $salvage = (float) ($asset->salvage_value ?? 0);
                            $life = max((int) ($asset->useful_life_years ?? 1), 1);
                            $method = $asset->depreciation_method ?? 'straight_line';

                            $purchaseDate = $asset->purchase_date
                                ? \Carbon\Carbon::parse($asset->purchase_date)
                                : now();

                            $ageYears = max(0, $purchaseDate->diffInDays(now()) / 365);

                            if ($method === 'reducing_balance') {
                                $rate = 1 / $life;
                                $computedCurrentValue = $cost * pow((1 - $rate), $ageYears);
                                $computedCurrentValue = max($salvage, $computedCurrentValue);
                                $computedDepreciation = max(0, $cost - $computedCurrentValue);
                            } elseif ($method === 'manual') {
                                $computedCurrentValue = (float) ($asset->current_value ?? $cost);
                                $computedDepreciation = max(0, $cost - $computedCurrentValue);
                            } else {
                                $annualDep = ($cost - $salvage) / $life;
                                $computedDepreciation = min(($cost - $salvage), $annualDep * $ageYears);
                                $computedCurrentValue = max($salvage, $cost - $computedDepreciation);
                            }
                        @endphp

                        <div class="p-4">

                            <div class="flex flex-col md:flex-row md:justify-between gap-4">

                                <div>
                                    <div class="flex flex-wrap gap-2 items-center">
                                        <p class="font-mono font-black text-green-700">{{ $asset->asset_code }}</p>

                                        <span class="px-2 py-1 text-[10px] font-bold uppercase border bg-slate-50 border-slate-300">
                                            {{ $asset->asset_category }}
                                        </span>

                                        <span class="px-2 py-1 text-[10px] font-bold uppercase border bg-blue-50 text-blue-700 border-blue-300">
                                            {{ str_replace('_', ' ', $asset->status) }}
                                        </span>

                                        <span class="px-2 py-1 text-[10px] font-bold uppercase border bg-purple-50 text-purple-700 border-purple-300">
                                            {{ str_replace('_', ' ', $method) }}
                                        </span>
                                    </div>

                                    <p class="text-sm font-black mt-1">{{ $asset->asset_name }}</p>

                                    <p class="text-[10px] text-slate-500 mt-1">
                                        Serial: {{ $asset->serial_number ?: '-' }}
                                        |
                                        Custodian: {{ $asset->custodian ?: '-' }}
                                        |
                                        Project: {{ $asset->project_name ?? 'No Project' }}
                                    </p>

                                    <p class="text-[10px] text-slate-500">
                                        Location: {{ $asset->location ?: '-' }}
                                        |
                                        Age: {{ number_format($ageYears, 2) }} year(s)
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                            wire:click="editAsset({{ $asset->id }})"
                                            class="bg-blue-50 text-blue-700 border border-blue-300 px-3 py-1.5 text-[10px] font-bold">
                                        Edit
                                    </button>

                                    <button type="button"
                                            wire:click="selectAssetForMovement({{ $asset->id }})"
                                            class="bg-purple-50 text-purple-700 border border-purple-300 px-3 py-1.5 text-[10px] font-bold">
                                        Operation
                                    </button>

                                    <button type="button"
                                            wire:click="deleteAsset({{ $asset->id }})"
                                            onclick="return confirm('Delete this asset?')"
                                            class="bg-red-50 text-red-700 border border-red-300 px-3 py-1.5 text-[10px] font-bold">
                                        Delete
                                    </button>
                                </div>
                            </div>

                            {{-- ASSET VALUES - HORIZONTAL CARDS --}}
                            <div class="w-full mt-4 overflow-x-auto">
                                <div class="flex gap-3 min-w-max">

                                    <div class="w-44 bg-slate-50 border border-slate-200 p-3">
                                        <p class="text-[10px] uppercase font-bold text-slate-500">Cost</p>
                                        <p class="font-mono font-black">{{ number_format($cost, 2) }}</p>
                                    </div>

                                    <div class="w-44 bg-slate-50 border border-slate-200 p-3">
                                        <p class="text-[10px] uppercase font-bold text-slate-500">Stored Value</p>
                                        <p class="font-mono font-black text-slate-700">
                                            {{ number_format((float) ($asset->current_value ?? 0), 2) }}
                                        </p>
                                    </div>

                                    <div class="w-44 bg-slate-50 border border-slate-200 p-3">
                                        <p class="text-[10px] uppercase font-bold text-slate-500">Computed Value</p>
                                        <p class="font-mono font-black text-green-700">
                                            {{ number_format($computedCurrentValue, 2) }}
                                        </p>
                                    </div>

                                    <div class="w-44 bg-slate-50 border border-slate-200 p-3">
                                        <p class="text-[10px] uppercase font-bold text-slate-500">Computed Dep.</p>
                                        <p class="font-mono font-black text-red-700">
                                            {{ number_format($computedDepreciation, 2) }}
                                        </p>
                                    </div>

                                    <div class="w-44 bg-slate-50 border border-slate-200 p-3">
                                        <p class="text-[10px] uppercase font-bold text-slate-500">Salvage</p>
                                        <p class="font-mono font-black">{{ number_format($salvage, 2) }}</p>
                                    </div>

                                    <div class="w-44 bg-slate-50 border border-slate-200 p-3">
                                        <p class="text-[10px] uppercase font-bold text-slate-500">Useful Life</p>
                                        <p class="font-mono font-black">{{ $life }} year(s)</p>
                                    </div>

                                </div>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                @if($asset->asset_photo_path)
                                    <a href="{{ asset('storage/'.$asset->asset_photo_path) }}"
                                       target="_blank"
                                       class="px-3 py-1.5 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">
                                        View Picture
                                    </a>
                                @endif

                                @if($asset->asset_document_path)
                                    <a href="{{ asset('storage/'.$asset->asset_document_path) }}"
                                       target="_blank"
                                       class="px-3 py-1.5 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-300">
                                        View Document
                                    </a>
                                @endif
                            </div>

                        </div>

                    @empty

                        <div class="p-10 text-center text-slate-400 font-bold">
                            No fixed assets found.
                        </div>

                    @endforelse

                </div>
            </div>
        </div>

    @elseif($activeTab === 'operations')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
                <div class="bg-slate-900 text-white px-4 py-3">
                    <p class="text-[10px] uppercase tracking-widest text-purple-300 font-bold">Asset Operations</p>
                    <h2 class="text-sm font-black">Transfer / Depreciate / Dispose</h2>
                </div>

                <form wire:submit.prevent="saveMovement" class="p-5 space-y-4">

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Asset</label>
                        <select wire:model="movement_asset_id"
                                class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">Select Asset</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}">
                                    {{ $asset->asset_code }} — {{ $asset->asset_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Operation</label>
                            <select wire:model="movement_type"
                                    class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                                @foreach($movementTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-500">Date</label>
                            <input type="date"
                                   wire:model="movement_date"
                                   class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" wire:model="from_location" placeholder="From Location"
                               class="border border-slate-300 px-3 py-2 text-xs">

                        <input type="text" wire:model="to_location" placeholder="To Location"
                               class="border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" wire:model="from_custodian" placeholder="From Custodian"
                               class="border border-slate-300 px-3 py-2 text-xs">

                        <input type="text" wire:model="to_custodian" placeholder="To Custodian"
                               class="border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <select wire:model="from_project_id"
                                class="border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">From Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>

                        <select wire:model="to_project_id"
                                class="border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">To Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Amount / Depreciation / Disposal Value</label>
                        <input type="number"
                               step="0.01"
                               wire:model="movement_amount"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <textarea wire:model="movement_remarks"
                              rows="3"
                              placeholder="Remarks"
                              class="w-full border border-slate-300 px-3 py-2 text-xs resize-none"></textarea>

                    <button type="submit"
                            class="bg-purple-700 text-white border border-purple-800 px-4 py-2 text-xs font-bold"
                            @disabled(! $hasFixedAssetTables)>
                        Save Operation
                    </button>
                </form>
            </div>

            <div class="xl:col-span-2 bg-white border border-slate-300 p-5">
                <h2 class="text-sm font-black mb-4">Synchronization Rules</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="border border-slate-300 p-4 bg-slate-50">
                        <b>Asset Purchase:</b> Dr Fixed Assets, Cr Accounts Payable.
                    </div>

                    <div class="border border-slate-300 p-4 bg-slate-50">
                        <b>Depreciation:</b> Dr Depreciation Expense, Cr Accumulated Depreciation.
                    </div>

                    <div class="border border-slate-300 p-4 bg-slate-50">
                        <b>Transfer:</b> No GL effect, register only.
                    </div>

                    <div class="border border-slate-300 p-4 bg-slate-50">
                        <b>Disposal/Loss:</b> Register effect now; GL method can be added in FinanceCoordinator.
                    </div>
                </div>
            </div>
        </div>

    @elseif($activeTab === 'movements')

        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Audit Trail</p>
                <h2 class="text-sm font-black">Fixed Asset Movements</h2>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse($movements as $movement)
                    <div class="p-4">
                        <div class="flex justify-between gap-4">
                            <div>
                                <p class="font-mono font-black text-green-700">{{ $movement->asset_code }}</p>
                                <p class="text-sm font-black">{{ $movement->asset_name }}</p>
                                <p class="text-[10px] text-slate-500">
                                    {{ \Carbon\Carbon::parse($movement->movement_date)->format('d M Y') }}
                                    |
                                    {{ str_replace('_', ' ', $movement->movement_type) }}
                                </p>
                            </div>

                            <p class="font-mono font-black">
                                {{ number_format((float) $movement->amount, 2) }}
                            </p>
                        </div>

                        <p class="text-xs mt-2">
                            {{ $movement->from_location ?: '-' }} → {{ $movement->to_location ?: '-' }}
                            |
                            {{ $movement->from_custodian ?: '-' }} → {{ $movement->to_custodian ?: '-' }}
                        </p>

                        @if($movement->remarks)
                            <div class="mt-3 bg-slate-50 border border-slate-200 p-3 text-xs">
                                {{ $movement->remarks }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 font-bold">
                        No movements found.
                    </div>
                @endforelse
            </div>
        </div>

    @endif
</div>