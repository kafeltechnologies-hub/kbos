<div class="min-h-screen bg-slate-100 p-6 text-slate-900">

    @include('livewire.finance._header', [
        'title' => 'Budget Centre',
        'subtitle' => 'Create, edit, monitor and compare budgets against actual General Ledger postings.'
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

    {{-- KPI CARDS --}}
    <div class="w-full mb-6 overflow-x-auto">
        <div class="flex gap-4 min-w-max">

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Total Budget</p>
                <p class="text-2xl font-black font-mono text-blue-700">{{ number_format($totalBudget ?? 0, 2) }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Actual Spent</p>
                <p class="text-2xl font-black font-mono text-red-700">{{ number_format($totalActual ?? 0, 2) }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Variance</p>
                <p class="text-2xl font-black font-mono {{ ($totalVariance ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                    {{ number_format($totalVariance ?? 0, 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Budget Items</p>
                <p class="text-2xl font-black font-mono">{{ $budgets?->count() ?? 0 }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Over Budget</p>
                <p class="text-2xl font-black font-mono text-red-700">{{ $overBudgetCount ?? 0 }}</p>
            </div>

        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- FORM --}}
        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">

            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Budget Item</p>
                <h2 class="text-sm font-black">{{ $editingId ? 'Edit Budget' : 'Create Budget' }}</h2>
            </div>

            <form wire:submit.prevent="saveBudget" class="p-5 space-y-4">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Budget Code</label>
                        <input type="text" wire:model="budget_code" placeholder="Auto if blank"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Year</label>
                        <input type="number" wire:model="form_financial_year"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-500">Budget Name</label>
                    <input type="text" wire:model="budget_name"
                           class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                </div>

                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-500">Project</label>
                    <select wire:model="form_project_id"
                            class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                        <option value="">No Project</option>
                        @foreach($projects ?? [] as $project)
                            <option value="{{ $project->id }}">
                                {{ $project->project_code ?? 'PRJ-'.$project->id }} — {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-500">Account</label>
                    <select wire:model="form_account_id"
                            class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                        <option value="">Select Account</option>
                        @foreach($accounts ?? [] as $account)
                            <option value="{{ $account->id }}">
                                {{ $account->account_code }} — {{ $account->account_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Period</label>
                        <select wire:model="form_period"
                                class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="annual">Annual</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Status</label>
                        <select wire:model="form_status"
                                class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                            <option value="closed">Closed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Budget Amount</label>
                        <input type="number" step="0.01" wire:model="budget_amount"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Alert %</label>
                        <input type="number" step="0.01" wire:model="alert_threshold"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-500">Description</label>
                    <textarea wire:model="description" rows="3"
                              class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs resize-none"></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-green-700 text-white border border-green-800 px-4 py-2 text-xs font-bold">
                        {{ $editingId ? 'Update Budget' : 'Save Budget' }}
                    </button>

                    <button type="button" wire:click="clearForm"
                            class="bg-white border border-slate-300 px-4 py-2 text-xs font-bold">
                        Clear
                    </button>
                </div>
            </form>
        </div>

        {{-- REGISTER --}}
        <div class="xl:col-span-2 bg-white border border-slate-300 shadow-sm overflow-hidden">

            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Budget Register</p>
                <h2 class="text-sm font-black">Budget vs Actual</h2>
            </div>

            {{-- HORIZONTAL FILTERS --}}
            <div class="bg-slate-50 border-b border-slate-300 p-4 overflow-x-auto">
                <div class="flex gap-3 min-w-max items-center">

                    <input type="text"
                           wire:model.live.debounce.500ms="search"
                           placeholder="Search budget, project, account..."
                           class="w-72 border border-slate-300 px-3 py-2 text-xs">

                    <select wire:model.live="filter_year"
                            class="w-40 border border-slate-300 px-3 py-2 text-xs bg-white">
                        <option value="">All Years</option>
                        @foreach($years ?? [] as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="filter_status"
                            class="w-44 border border-slate-300 px-3 py-2 text-xs bg-white">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="draft">Draft</option>
                        <option value="closed">Closed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>

                    <select wire:model.live="filter_project_id"
                            class="w-56 border border-slate-300 px-3 py-2 text-xs bg-white">
                        <option value="">All Projects</option>
                        @foreach($projects ?? [] as $project)
                            <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                        @endforeach
                    </select>

                    <button type="button" wire:click="clearFilters"
                            class="w-36 bg-white border border-slate-300 px-4 py-2 text-xs font-bold">
                        Clear Filters
                    </button>

                </div>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse($budgets ?? [] as $budget)
                    <div class="p-4">

                        <div class="flex flex-col md:flex-row md:justify-between gap-4">

                            <div>
                                <div class="flex flex-wrap gap-2 items-center">
                                    <p class="font-mono font-black text-green-700">{{ $budget->budget_code }}</p>

                                    <span class="px-2 py-1 text-[10px] uppercase font-bold border bg-slate-50 border-slate-300">
                                        {{ $budget->financial_year }}
                                    </span>

                                    <span class="px-2 py-1 text-[10px] uppercase font-bold border bg-blue-50 text-blue-700 border-blue-300">
                                        {{ $budget->status }}
                                    </span>
                                </div>

                                <p class="text-sm font-black mt-1">{{ $budget->budget_name }}</p>
                                <p class="text-[10px] text-slate-500 mt-1">
                                    Account: {{ $budget->account_code }} — {{ $budget->account_name }}
                                </p>
                                <p class="text-[10px] text-slate-500">
                                    Project: {{ $budget->project_name ?? 'No Project' }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                        wire:click="editBudget({{ $budget->id }})"
                                        class="bg-blue-50 text-blue-700 border border-blue-300 px-3 py-1.5 text-[10px] font-bold">
                                    Edit
                                </button>

                                <button type="button"
                                        wire:click="deleteBudget({{ $budget->id }})"
                                        onclick="return confirm('Delete this budget?')"
                                        class="bg-red-50 text-red-700 border border-red-300 px-3 py-1.5 text-[10px] font-bold">
                                    Delete
                                </button>
                            </div>
                        </div>

                        @php
                            $budgetAmount = (float)($budget->budget_amount ?? 0);
                            $actualAmount = (float)($budget->actual_amount ?? 0);
                            $variance = $budgetAmount - $actualAmount;
                            $usage = $budgetAmount > 0 ? ($actualAmount / $budgetAmount) * 100 : 0;
                            $alert = (float)($budget->alert_threshold ?? 80);
                        @endphp

                        <div class="w-full mt-4 overflow-x-auto">
                            <div class="flex gap-3 min-w-max">

                                <div class="w-44 bg-slate-50 border border-slate-200 p-3">
                                    <p class="text-[10px] uppercase font-bold text-slate-500">Budget</p>
                                    <p class="font-mono font-black text-blue-700">{{ number_format($budgetAmount, 2) }}</p>
                                </div>

                                <div class="w-44 bg-slate-50 border border-slate-200 p-3">
                                    <p class="text-[10px] uppercase font-bold text-slate-500">Actual</p>
                                    <p class="font-mono font-black text-red-700">{{ number_format($actualAmount, 2) }}</p>
                                </div>

                                <div class="w-44 bg-slate-50 border border-slate-200 p-3">
                                    <p class="text-[10px] uppercase font-bold text-slate-500">Variance</p>
                                    <p class="font-mono font-black {{ $variance >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                        {{ number_format($variance, 2) }}
                                    </p>
                                </div>

                                <div class="w-44 bg-slate-50 border border-slate-200 p-3">
                                    <p class="text-[10px] uppercase font-bold text-slate-500">Usage</p>
                                    <p class="font-mono font-black {{ $usage >= $alert ? 'text-red-700' : 'text-green-700' }}">
                                        {{ number_format($usage, 2) }}%
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 font-bold">
                        No budget items found.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>