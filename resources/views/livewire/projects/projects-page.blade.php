<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

    <form id="project-form" wire:submit.prevent="postLedger">

        <div class="border border-slate-300 bg-white shadow-sm rounded-none overflow-hidden">

            <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-4 py-3 flex items-center justify-between border-b border-slate-900">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase font-mono block">
                        Projects Area
                    </span>

                    <h1 class="text-sm font-bold text-white">
                        Master Data — Project Ledger Registry
                    </h1>
                </div>

                <div class="hidden sm:block border-l border-slate-600 pl-4 text-right">
                    <span class="text-[10px] block uppercase font-mono text-slate-400">
                        Project Count
                    </span>

                    <span class="text-base font-black font-mono text-white">
                        {{ $projects->count() }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 bg-slate-50 px-3 py-2">

                <button type="button" wire:click="createNew()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 hover:text-blue-700 active:bg-slate-200 transition rounded-none shadow-sm">
                    Create New
                </button>

                <button type="submit"
                    class="inline-flex items-center gap-1 px-4 py-1.5 text-xs font-semibold text-white bg-blue-700 border border-blue-800 hover:bg-blue-800 active:bg-blue-900 transition rounded-none shadow-sm">
                    Post Ledger
                </button>

                <button type="button" wire:click="clearBuffer()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 hover:text-slate-800 active:bg-slate-200 transition rounded-none shadow-sm">
                    Clear Buffer
                </button>

                <button type="button" wire:click="sync()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 hover:text-slate-800 active:bg-slate-200 transition rounded-none shadow-sm">
                    Sync
                </button>

                <div class="ml-auto px-3 py-1 bg-slate-200 border border-slate-300 text-[11px] font-mono font-bold text-slate-700 rounded-none">
                    SYSTEM GENERATED ID:
                    <span class="text-blue-700">
                        {{ $this->generateProjectCode() }}
                    </span>
                </div>

            </div>
        </div>

        @if (session()->has('success'))
            <div class="mt-4 border-l-4 border-green-600 bg-green-50 p-3 text-xs font-medium text-green-900 rounded-none shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('info'))
            <div class="mt-4 border-l-4 border-blue-600 bg-blue-50 p-3 text-xs font-medium text-blue-900 rounded-none shadow-sm">
                {{ session('info') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 border-l-4 border-red-600 bg-red-50 p-3 text-xs font-medium text-red-900 rounded-none shadow-sm">
                Please correct the highlighted fields.
            </div>
        @endif

        <div class="mt-6 border border-slate-300 bg-white rounded-none shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                    01. Project Identity Block
                </h2>
            </div>

            <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                <div class="flex items-center w-full">
                    <label for="company_id" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Company <span class="text-red-500">*</span>
                    </label>

                    <div class="flex-1 min-w-0">
                        <select id="company_id" name="company_id" wire:model="company_id"
                            class="w-full text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
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
                    <label for="branch_id" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Branch
                    </label>

                    <select id="branch_id" name="branch_id" wire:model="branch_id"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                        <option value="">Select branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center w-full">
                    <label for="project_name" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Project Name <span class="text-red-500">*</span>
                    </label>

                    <div class="flex-1 min-w-0">
                        <input id="project_name" name="project_name" wire:model="project_name" type="text"
                            class="w-full text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">

                        @error('project_name')
                            <p class="mt-1 text-[11px] font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center w-full">
                    <label for="project_type" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Project Type
                    </label>

                    <select id="project_type" name="project_type" wire:model="project_type"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                        <option value="">Select type</option>
                        @foreach($projectTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center w-full">
                    <label for="client_id" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Client
                    </label>

                    <select id="client_id" name="client_id" wire:model="client_id"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                        <option value="">Select client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center w-full">
                    <label for="location" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Location
                    </label>

                    <input id="location" name="location" wire:model="location" type="text"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                </div>

            </div>
        </div>

        <div class="mt-8 border border-slate-300 bg-white rounded-none shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                    02. Financial Control Block
                </h2>
            </div>

            <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                <div class="flex items-center w-full">
                    <label for="contract_amount" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Contract Amount
                    </label>

                    <input id="contract_amount" name="contract_amount" wire:model="contract_amount" type="number" step="0.01"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                </div>

                <div class="flex items-center w-full">
                    <label for="budget_amount" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Budget Amount
                    </label>

                    <input id="budget_amount" name="budget_amount" wire:model="budget_amount" type="number" step="0.01"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                </div>

                <div class="flex items-center w-full">
                    <label for="cost_center_id" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Cost Center
                    </label>

                    <select id="cost_center_id" name="cost_center_id" wire:model="cost_center_id"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                        <option value="">Select cost center</option>
                        @foreach($costCenters as $costCenter)
                            <option value="{{ $costCenter->id }}">{{ $costCenter->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center w-full">
                    <label for="status" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Status
                    </label>

                    <select id="status" name="status" wire:model="status"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption }}">
                                {{ strtoupper(str_replace('_', ' ', $statusOption)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        <div class="mt-8 border border-slate-300 bg-white rounded-none shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                    03. Timeline & Description Block
                </h2>
            </div>

            <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                <div class="flex items-center w-full">
                    <label for="start_date" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Start Date
                    </label>

                    <input id="start_date" name="start_date" wire:model="start_date" type="date"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                </div>

                <div class="flex items-center w-full">
                    <label for="expected_end_date" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Expected End
                    </label>

                    <input id="expected_end_date" name="expected_end_date" wire:model="expected_end_date" type="date"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                </div>

                <div class="flex items-start w-full xl:col-span-2">
                    <label for="description" class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                        Description
                    </label>

                    <textarea id="description" name="description" wire:model="description" rows="2"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition resize-none"></textarea>
                </div>

            </div>
        </div>

    </form>

    <div class="mt-8 border border-slate-300 bg-white shadow-sm rounded-none overflow-hidden">

        <div class="bg-slate-800 px-4 py-3 border-b border-slate-900">
            <h2 class="text-xs font-bold uppercase tracking-wider text-white">
                Project Ledger Outputs
            </h2>
        </div>

        <div class="w-full bg-slate-200 border-b border-slate-300 flex items-center shadow-inner">
            <span class="pl-4 text-slate-500 font-mono text-sm select-none">🔍</span>

            <input id="search" name="search" wire:model.live.debounce.500ms="search" type="text"
                placeholder="Filter project records inline..."
                class="w-full bg-transparent border-0 px-3 py-3 text-xs text-slate-900 placeholder-slate-500 focus:ring-0 outline-none font-medium">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap table-fixed">

                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300 select-none">
                    <tr>
                        <th class="w-28 px-4 py-4 border-r border-slate-200">Project Code</th>
                        <th class="w-64 px-4 py-4 border-r border-slate-200">Project Name</th>
                        <th class="w-40 px-4 py-4 border-r border-slate-200">Client</th>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Contract</th>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Budget</th>
                        <th class="w-32 px-4 py-4">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($projects as $project)
                        <tr class="hover:bg-blue-50/70 border-b border-slate-200 transition">
                            <td class="px-4 py-6 font-mono font-bold text-blue-800 border-r border-slate-200 bg-slate-50/50">
                                {{ $project->project_code ?? '-' }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">
                                    {{ $project->project_name ?? '-' }}
                                </div>

                                <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5">
                                    {{ $project->project_type ?: 'UNCLASSIFIED' }}
                                </div>
                            </td>

                            <td class="px-4 py-6 text-slate-600 border-r border-slate-200">
                                {{ $project->client?->name ?: '-' }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono border-r border-slate-200">
                                {{ number_format((float) $project->contract_amount, 2) }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono border-r border-slate-200">
                                {{ number_format((float) $project->budget_amount, 2) }}
                            </td>

                            <td class="px-4 py-6">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-none text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-700 border border-slate-300">
                                    {{ strtoupper(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                [Err] 0 project records returned based on query arguments.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>