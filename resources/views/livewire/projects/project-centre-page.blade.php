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

        $phaseOptions = [
            'Project Initiation',
            'Site Survey',
            'Design & Engineering',
            'Permits & Approvals',
            'Procurement',
            'Civil Works',
            'Installation',
            'Testing & Commissioning',
            'Client Handover',
            'Project Closure',
        ];

        $wbsOptions = [
            'Project Initiation',
            'Site Survey',
            'Engineering Design',
            'Permits and Approvals',
            'Procurement',
            'Material Inspection',
            'Civil Works',
            'Pole Erection',
            'Cable Laying',
            'Conductor Stringing',
            'Transformer Installation',
            'Earthing System',
            'Protection Installation',
            'Testing and Commissioning',
            'Documentation',
            'Client Handover',
            'Project Closure',
        ];
    @endphp

    <div class="border border-slate-300 bg-white shadow-sm overflow-hidden mb-6">
        <div class="bg-slate-950 px-4 py-3">
            <p class="text-[10px] font-bold uppercase tracking-widest text-green-300">
                Project Module
            </p>
            <h1 class="text-sm font-black text-white">
                Project Control Centre
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

    <form id="project-centre-form" wire:submit.prevent="save">

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-green-900 px-4 py-3 flex items-center justify-between border-b border-slate-950">
                <div>
                    <span class="text-[10px] font-bold text-green-200 tracking-wider uppercase font-mono block">
                        Project Control Area
                    </span>

                    <h1 class="text-sm font-bold text-white">
                        Project Centre — Scope, Phases, WBS, Deliverables, Materials, Budget & Value
                    </h1>
                </div>

                <div class="hidden sm:block border-l border-slate-600 pl-4 text-right">
                    <span class="text-[10px] block uppercase font-mono text-slate-300">Project Records</span>
                    <span class="text-base font-black font-mono text-white">{{ $projects->count() }}</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 bg-slate-50 px-3 py-2 border-b border-slate-200">
                <button type="button" wire:click="createNew()" class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Create New
                </button>

                <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-green-700 border border-green-800 hover:bg-green-800 shadow-sm">
                    {{ $isEditing ? 'Update Project' : 'Create Project' }}
                </button>

                <button type="button" wire:click="addProjectPhase()" class="px-3 py-1.5 text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-300 hover:bg-purple-100 shadow-sm">
                    Add Phase
                </button>

                <button type="button" wire:click="addWbsItem()" class="px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-300 hover:bg-blue-100 shadow-sm">
                    Add WBS
                </button>

                <button type="button" wire:click="addDeliverable()" class="px-3 py-1.5 text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-300 hover:bg-purple-100 shadow-sm">
                    Add Deliverable
                </button>

                <button type="button" wire:click="addProjectMaterial()" class="px-3 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-300 hover:bg-amber-100 shadow-sm">
                    Add Material
                </button>

                <button type="button" wire:click="addBudgetLine()" class="px-3 py-1.5 text-xs font-semibold text-cyan-700 bg-cyan-50 border border-cyan-300 hover:bg-cyan-100 shadow-sm">
                    Add Budget
                </button>

                <button type="button" wire:click="clearBuffer()" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Clear
                </button>

                <button type="button" wire:click="sync()" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Sync
                </button>

                <div class="ml-auto px-3 py-1 bg-green-50 border border-green-200 text-[11px] font-mono font-bold text-slate-700">
                    PROJECT NO:
                    <span class="text-green-700">
                        {{ $isEditing ? 'EDIT MODE' : $this->generateProjectCode() }}
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

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">

            <div class="xl:col-span-2 space-y-6">

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            01. Project Header
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Project Name <span class="text-red-500">*</span>
                            </label>

                            <input type="text" wire:model="project_name"
                                class="flex-1 text-xs bg-white border border-green-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Project Type
                            </label>

                            <select wire:model.live="project_type"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                                <option value="">Select project type</option>
                                @foreach($projectTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Company
                            </label>

                            <select wire:model="company_id"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                                <option value="">Select company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Client
                            </label>

                            <select wire:model="client_id"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                                <option value="">Select client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Status
                            </label>

                            <select wire:model="status"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption }}">{{ strtoupper(str_replace('_', ' ', $statusOption)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Priority
                            </label>

                            <select wire:model="priority"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                                @foreach($priorities as $priorityOption)
                                    <option value="{{ $priorityOption }}">{{ strtoupper($priorityOption) }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            02. Project Scope & Objectives
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 gap-y-5">
                        <div class="flex items-start w-full">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Scope Summary
                            </label>

                            <textarea wire:model="scope_summary" rows="3"
                                placeholder="Brief description of what the project covers..."
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>

                        <div class="flex items-start w-full">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Objectives
                            </label>

                            <textarea wire:model="objectives" rows="3"
                                placeholder="Main goals, measurable targets, expected outcomes..."
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>

                        <div class="flex items-start w-full">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Location
                            </label>

                            <textarea wire:model="location" rows="2"
                                placeholder="Project site, district, region, GPS/address..."
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            03. Timeline & Responsibility
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Start Date
                            </label>

                            <input type="date" wire:model.live="start_date"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                End Date
                            </label>

                            <input type="date" wire:model.live="end_date"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Duration
                            </label>

                            <input value="{{ $duration_days }} days" readonly
                                class="flex-1 text-xs bg-slate-200 border border-slate-300 px-2.5 py-1.5 shadow-inner text-slate-700 cursor-not-allowed">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Project Manager
                            </label>

                            <input type="text" wire:model="project_manager"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Site Engineer
                            </label>

                            <input type="text" wire:model="site_engineer"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Client Rep.
                            </label>

                            <input type="text" wire:model="client_representative"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                        </div>
                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200 flex items-center justify-between">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            04. Project Phases
                        </h2>

                        <button type="button" wire:click="addProjectPhase()"
                            class="px-2 py-1 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-300">
                            Add Phase
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-slate-700 border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-3 w-28">Code</th>
                                    <th class="px-3 py-3 min-w-[240px]">Phase</th>
                                    <th class="px-3 py-3 min-w-[280px]">Description</th>
                                    <th class="px-3 py-3 min-w-[120px]">Start</th>
                                    <th class="px-3 py-3 min-w-[120px]">End</th>
                                    <th class="px-3 py-3 min-w-[160px]">Responsible</th>
                                    <th class="px-3 py-3 w-32">Budget</th>
                                    <th class="px-3 py-3 w-32">Actual</th>
                                    <th class="px-3 py-3 w-28">Progress %</th>
                                    <th class="px-3 py-3 w-32">Status</th>
                                    <th class="px-3 py-3 w-20">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200">
                                @foreach($projectPhases as $index => $item)
                                    @php
                                        $autoPhaseCode = $item['phase_code'] ?: 'PH-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                                    @endphp

                                    <tr>
                                        <td class="px-3 py-3 align-top">
                                            <input type="text" value="{{ $autoPhaseCode }}" readonly
                                                class="w-full text-xs bg-slate-200 border border-slate-300 px-2 py-1.5 font-mono font-bold text-slate-700 cursor-not-allowed">
                                                
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <select wire:model="projectPhases.{{ $index }}.phase_name"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                                <option value="">Select phase</option>
                                                @foreach($phaseOptions as $phase)
                                                    <option value="{{ $phase }}">{{ $phase }}</option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <textarea wire:model="projectPhases.{{ $index }}.description" rows="2"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5 resize-none"></textarea>
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input type="date" wire:model="projectPhases.{{ $index }}.start_date"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input type="date" wire:model="projectPhases.{{ $index }}.end_date"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input type="text" wire:model="projectPhases.{{ $index }}.responsible_person"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input type="number" step="0.01" wire:model.live="projectPhases.{{ $index }}.budget_amount"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input type="number" step="0.01" wire:model.live="projectPhases.{{ $index }}.actual_cost"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input type="number" step="0.01" wire:model="projectPhases.{{ $index }}.progress_percent"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <select wire:model="projectPhases.{{ $index }}.status"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                                @foreach($wbsStatuses as $statusOption)
                                                    <option value="{{ $statusOption }}">
                                                        {{ strtoupper(str_replace('_', ' ', $statusOption)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <button type="button" wire:click="removeProjectPhase({{ $index }})"
                                                class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200 flex items-center justify-between">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            05. Work Breakdown Structure
                        </h2>

                        <button type="button" wire:click="addWbsItem()"
                            class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">
                            Add WBS
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-slate-700 border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-3 w-28">WBS Code</th>
                                    <th class="px-3 py-3 min-w-[260px]">WBS Item</th>
                                    <th class="px-3 py-3 min-w-[280px]">Description</th>
                                    <th class="px-3 py-3 min-w-[160px]">Responsible</th>
                                    <th class="px-3 py-3 w-32">Budget</th>
                                    <th class="px-3 py-3 w-28">Progress %</th>
                                    <th class="px-3 py-3 w-32">Status</th>
                                    <th class="px-3 py-3 w-20">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200">
                                @foreach($wbsItems as $index => $item)
                                    @php
                                        $autoWbsCode = $item['wbs_code'] ?: 'WBS-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                                    @endphp

                                    <tr>
                                        <td class="px-3 py-3 align-top">
                                            <input type="text" value="{{ $autoWbsCode }}" readonly
                                                class="w-full text-xs bg-slate-200 border border-slate-300 px-2 py-1.5 font-mono font-bold text-slate-700 cursor-not-allowed">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <select wire:model="wbsItems.{{ $index }}.title"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                                <option value="">Select WBS item</option>
                                                @foreach($wbsOptions as $wbs)
                                                    <option value="{{ $wbs }}">{{ $wbs }}</option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <textarea wire:model="wbsItems.{{ $index }}.description" rows="2"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5 resize-none"></textarea>
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input type="text" wire:model="wbsItems.{{ $index }}.responsible_person"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input type="number" step="0.01" wire:model.live="wbsItems.{{ $index }}.budget_amount"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input type="number" step="0.01" wire:model="wbsItems.{{ $index }}.progress_percent"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <select wire:model="wbsItems.{{ $index }}.status"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                                @foreach($wbsStatuses as $statusOption)
                                                    <option value="{{ $statusOption }}">
                                                        {{ strtoupper(str_replace('_', ' ', $statusOption)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <button type="button" wire:click="removeWbsItem({{ $index }})"
                                                class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Keep your existing Deliverables, Materials, Budget, Notes and Project Ledger blocks below --}}

            </div>

            <div class="xl:col-span-1">
                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden sticky top-6">
                    <div class="bg-slate-800 px-3 py-2 border-b border-slate-900">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-white">
                            Project Snapshot
                        </h2>
                    </div>

                    <div class="p-5 space-y-4">

                        <div class="bg-green-50 border border-green-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-green-700">Contract Value</p>

                            <input type="number" step="0.01" wire:model.live="contract_amount"
                                class="mt-2 w-full text-xl font-black font-mono bg-white border border-green-300 px-2.5 py-2 text-green-900 focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="bg-red-50 border border-red-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-red-700">Estimated Cost</p>

                            <input type="number" step="0.01" wire:model.live="estimated_cost"
                                class="mt-2 w-full text-xl font-black font-mono bg-white border border-red-300 px-2.5 py-2 text-red-900 focus:border-red-600 focus:ring-0 outline-none">
                        </div>

                        <div class="bg-cyan-50 border border-cyan-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-cyan-700">Budget Amount</p>

                            <p class="mt-1 text-xl font-black font-mono text-cyan-900">
                                {{ number_format((float) $budget_amount, 2) }}
                            </p>
                        </div>

                        <div class="{{ (float) $expected_profit >= 0 ? 'bg-blue-50 border-blue-200' : 'bg-red-50 border-red-200' }} border p-4">
                            <p class="text-[10px] font-bold uppercase {{ (float) $expected_profit >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                                Expected Profit
                            </p>

                            <p class="mt-1 text-xl font-black font-mono {{ (float) $expected_profit >= 0 ? 'text-blue-900' : 'text-red-900' }}">
                                {{ number_format((float) $expected_profit, 2) }}
                            </p>
                        </div>

                        <div class="{{ (float) $profit_margin >= 0 ? 'bg-purple-50 border-purple-200' : 'bg-red-50 border-red-200' }} border p-4">
                            <p class="text-[10px] font-bold uppercase {{ (float) $profit_margin >= 0 ? 'text-purple-700' : 'text-red-700' }}">
                                Profit Margin
                            </p>

                            <p class="mt-1 text-xl font-black font-mono {{ (float) $profit_margin >= 0 ? 'text-purple-900' : 'text-red-900' }}">
                                {{ number_format((float) $profit_margin, 2) }}%
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-slate-600">Duration</p>

                            <p class="mt-1 text-xl font-black font-mono text-slate-900">
                                {{ $duration_days }} days
                            </p>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-amber-700">Material Cost</p>

                            <p class="mt-1 text-xl font-black font-mono text-amber-900">
                                {{ number_format(collect($projectMaterials)->sum(fn($i) => (float) ($i['line_total'] ?? 0)), 2) }}
                            </p>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </form>

</div>