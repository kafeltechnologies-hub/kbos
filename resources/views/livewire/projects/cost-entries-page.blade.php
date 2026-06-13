<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

    @php
        $projectNavLinks = [
            ['label' => 'Project Dashboard', 'route' => 'projects.dashboard'],
            ['label' => 'Project Centre', 'route' => 'projects.project-centre'],
            ['label' => 'Projects List', 'route' => 'projects.index'],
            ['label' => 'Cost Entries', 'route' => 'projects.cost-entries'],
            ['label' => 'Project Payments', 'route' => 'projects.payments'],
            ['label' => 'Project Receipts', 'route' => 'projects.receipts'],
            ['label' => 'Project Quotations', 'route' => 'projects.quotations'],
            ['label' => 'Finance Dashboard', 'route' => 'finance.dashboard'],
        ];

        $otherCostCategories = [
            'Labour',
            'Transport',
            'Equipment Hire',
            'Subcontractor',
            'Permits and Approvals',
            'Fuel',
            'Accommodation',
            'Communication',
            'Site Welfare',
            'Contingency',
            'Other Charges',
        ];
    @endphp

    <div class="border border-slate-300 bg-white shadow-sm overflow-hidden mb-6">
        <div class="bg-slate-950 px-4 py-3">
            <p class="text-[10px] font-bold uppercase tracking-widest text-green-300">
                Project Module
            </p>

            <h1 class="text-sm font-black text-white">
                Project Cost Control Centre
            </h1>
        </div>

        <div class="flex flex-wrap gap-2 p-3 bg-slate-900 border-t border-slate-800">
            @foreach($projectNavLinks as $link)
                @if(Route::has($link['route']))
                    <a href="{{ route($link['route']) }}"
                       class="px-3 py-2 text-xs font-bold border
                       {{ request()->routeIs($link['route'])
                            ? 'bg-green-600 text-white border-green-500'
                            : 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-700 hover:text-white' }}">
                        {{ $link['label'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <form id="cost-entry-form" wire:submit.prevent="postLedger">

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-4 py-3 flex items-center justify-between border-b border-slate-900">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase font-mono block">
                        Projects Area
                    </span>

                    <h1 class="text-sm font-bold text-white">
                        Project Cost Entry — Materials, Labour, Transport & Other Cost Items
                    </h1>
                </div>

                <div class="hidden sm:block border-l border-slate-600 pl-4 text-right">
                    <span class="text-[10px] block uppercase font-mono text-slate-400">
                        Cost Records
                    </span>

                    <span class="text-base font-black font-mono text-white">
                        {{ $costEntries->count() }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 bg-slate-50 px-3 py-2">

                <button type="button" wire:click="createNew()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Create New
                </button>

                <button type="submit"
                    class="px-4 py-1.5 text-xs font-semibold text-white bg-blue-700 border border-blue-800 hover:bg-blue-800 shadow-sm">
                    {{ $isEditing ?? false ? 'Update Ledger' : 'Post Ledger' }}
                </button>

                <button type="button" wire:click="addMaterialLine()"
                    class="px-3 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-300 hover:bg-amber-100 shadow-sm">
                    Add Material
                </button>

                <button type="button" wire:click="addOtherCostLine()"
                    class="px-3 py-1.5 text-xs font-semibold text-cyan-700 bg-cyan-50 border border-cyan-300 hover:bg-cyan-100 shadow-sm">
                    Add Other Cost
                </button>

                <button type="button" wire:click="clearBuffer()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Clear Buffer
                </button>

                <button type="button" wire:click="sync()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Sync
                </button>

                <div class="ml-auto px-3 py-1 bg-slate-200 border border-slate-300 text-[11px] font-mono font-bold text-slate-700">
                    COST ID:
                    <span class="text-blue-700">
                        {{ $isEditing ?? false ? ($cost_code ?? 'EDIT MODE') : $this->generateCostCode() }}
                    </span>
                </div>

            </div>
        </div>

        @if (session()->has('success'))
            <div class="mt-4 border-l-4 border-green-600 bg-green-50 p-3 text-xs font-medium text-green-900 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('info'))
            <div class="mt-4 border-l-4 border-blue-600 bg-blue-50 p-3 text-xs font-medium text-blue-900 shadow-sm">
                {{ session('info') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 border-l-4 border-red-600 bg-red-50 p-3 text-xs font-medium text-red-900 shadow-sm">
                Please correct the highlighted fields.
            </div>
        @endif

        <div class="mt-6 border border-slate-300 bg-white shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                    01. Cost Ownership Block
                </h2>
            </div>

            <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Company <span class="text-red-500">*</span>
                    </label>

                    <div class="flex-1 min-w-0">
                        <select wire:model="company_id"
                            class="w-full text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                            <option value="">Select company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>

                        @error('company_id')
                            <p class="mt-1 text-[11px] font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Project <span class="text-red-500">*</span>
                    </label>

                    <div class="flex-1 min-w-0">
                        <select wire:model.live="project_id"
                            class="w-full text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                            <option value="">Select project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">
                                    {{ $project->project_code }} — {{ $project->project_name }}
                                </option>
                            @endforeach
                        </select>

                        @error('project_id')
                            <p class="mt-1 text-[11px] font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Cost Center
                    </label>

                    <select wire:model="cost_center_id"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        <option value="">Select cost center</option>
                        @foreach($costCenters as $costCenter)
                            <option value="{{ $costCenter->id }}">{{ $costCenter->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Cost Date <span class="text-red-500">*</span>
                    </label>

                    <div class="flex-1 min-w-0">
                        <input wire:model="cost_date" type="date"
                            class="w-full text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">

                        @error('cost_date')
                            <p class="mt-1 text-[11px] font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Status
                    </label>

                    <select wire:model="status"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption }}">
                                {{ strtoupper($statusOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-start w-full xl:col-span-2">
                    <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                        Description
                    </label>

                    <textarea wire:model="description" rows="2"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none resize-none"></textarea>
                </div>

            </div>
        </div>

        <div class="mt-8 border border-slate-300 bg-white shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                    02. Project Materials From Material Table
                </h2>

                <button type="button" wire:click="addMaterialLine()"
                    class="px-2 py-1 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">
                    Add Material
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-3 min-w-[280px]">Material</th>
                            <th class="px-3 py-3 min-w-[300px]">Description</th>
                            <th class="px-3 py-3 w-24">Unit</th>
                            <th class="px-3 py-3 w-28">Qty</th>
                            <th class="px-3 py-3 w-32">Unit Cost</th>
                            <th class="px-3 py-3 w-32">Total</th>
                            <th class="px-3 py-3 w-20">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($materialLines ?? [] as $index => $line)
                            <tr>
                                <td class="px-3 py-3 align-top">
                                    <select
                                        wire:model.live="materialLines.{{ $index }}.material_id"
                                        wire:change="materialSelected({{ $index }})"
                                        class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        <option value="">Select material</option>

                                        @foreach(($materials ?? []) as $material)
                                            <option value="{{ $material->id }}">
                                                {{ $material->material_code ?? 'MAT' }} — {{ $material->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <div class="mt-1 text-[10px] font-mono text-slate-400">
                                        {{ $line['material_code'] ?? '' }}
                                    </div>
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <input
                                        type="text"
                                        wire:model="materialLines.{{ $index }}.description"
                                        readonly
                                        class="w-full text-xs bg-slate-100 border border-slate-300 px-2 py-1.5">
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <input
                                        type="text"
                                        wire:model="materialLines.{{ $index }}.unit"
                                        readonly
                                        class="w-full text-xs bg-slate-100 border border-slate-300 px-2 py-1.5">
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <input
                                        type="number"
                                        step="0.01"
                                        wire:model.live="materialLines.{{ $index }}.quantity"
                                        class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <input
                                        type="number"
                                        step="0.01"
                                        wire:model.live="materialLines.{{ $index }}.unit_cost"
                                        class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                </td>

                                <td class="px-3 py-3 align-top font-mono font-bold text-slate-700">
                                    {{
                                        number_format(
                                            (float) ($line['quantity'] ?? 0) *
                                            (float) ($line['unit_cost'] ?? 0),
                                            2
                                        )
                                    }}
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <button type="button" wire:click="removeMaterialLine({{ $index }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400 font-bold">
                                    No material lines added. Click “Add Material” to select materials from the material table.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 border border-slate-300 bg-white shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                    03. Other Cost Items
                </h2>

                <button type="button" wire:click="addOtherCostLine()"
                    class="px-2 py-1 text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-300">
                    Add Other Cost
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-3 min-w-[220px]">Cost Type</th>
                            <th class="px-3 py-3 min-w-[320px]">Description</th>
                            <th class="px-3 py-3 w-36">Amount</th>
                            <th class="px-3 py-3 w-20">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($otherCostLines ?? [] as $index => $line)
                            <tr>
                                <td class="px-3 py-3 align-top">
                                    <select wire:model="otherCostLines.{{ $index }}.cost_type"
                                        class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        <option value="">Select cost type</option>
                                        @foreach($otherCostCategories as $category)
                                            <option value="{{ $category }}">{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <textarea wire:model="otherCostLines.{{ $index }}.description" rows="2"
                                        class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5 resize-none"></textarea>
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <input type="number" step="0.01" wire:model.live="otherCostLines.{{ $index }}.amount"
                                        class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <button type="button" wire:click="removeOtherCostLine({{ $index }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-bold">
                                    No other cost items added.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </form>

    <div class="mt-8 border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3 border-b border-slate-900">
            <h2 class="text-xs font-bold uppercase tracking-wider text-white">
                Project Cost Ledger Outputs
            </h2>
        </div>

        <div class="w-full bg-slate-200 border-b border-slate-300 flex items-center shadow-inner">
            <span class="pl-4 text-slate-500 font-mono text-sm select-none">🔍</span>

            <input wire:model.live.debounce.500ms="search" type="text"
                placeholder="Filter cost records inline..."
                class="w-full bg-transparent border-0 px-3 py-3 text-xs text-slate-900 placeholder-slate-500 focus:ring-0 outline-none font-medium">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap table-fixed">

                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300 select-none">
                    <tr>
                        <th class="w-28 px-4 py-4 border-r border-slate-200">Cost Code</th>
                        <th class="w-56 px-4 py-4 border-r border-slate-200">Project</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Cost Type</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Amount</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Cost Date</th>
                        <th class="w-28 px-4 py-4 border-r border-slate-200">Status</th>
                        <th class="w-32 px-4 py-4">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($costEntries as $entry)
                        <tr class="hover:bg-blue-50/70 border-b border-slate-200 transition">
                            <td class="px-4 py-6 font-mono font-bold text-blue-800 border-r border-slate-200 bg-slate-50/50">
                                {{ $entry->cost_code ?? '-' }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">
                                    {{ $entry->project?->project_name ?: 'NO PROJECT' }}
                                </div>

                                <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5">
                                    {{ $entry->project?->project_code ?: 'GENERAL_COST' }}
                                </div>
                            </td>

                            <td class="px-4 py-6 text-slate-600 border-r border-slate-200 uppercase">
                                {{ $entry->cost_type ?: '-' }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono border-r border-slate-200">
                                {{ number_format((float) $entry->amount, 2) }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono border-r border-slate-200">
                                {{ $entry->cost_date }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-700 border border-slate-300">
                                    {{ strtoupper($entry->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-6">
                                <button type="button" wire:click="editCostEntry({{ $entry->id }})"
                                    class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">
                                    Edit
                                </button>

                                <button type="button" wire:click="cancelCostEntry({{ $entry->id }})"
                                    class="ml-1 px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                    Cancel
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                No cost records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>