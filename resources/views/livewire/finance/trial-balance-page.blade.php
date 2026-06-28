<div class="min-h-screen bg-slate-100 p-6 text-slate-900">

    @include('livewire.finance._header', [
        'title' => 'Trial Balance',
        'subtitle' => 'GL-based trial balance synchronized from all finance modules.'
    ])

    @include('livewire.finance._nav')

    {{-- HORIZONTAL KPI CARDS --}}
    <div class="w-full mb-6 overflow-x-auto">
        <div class="flex gap-4 min-w-max">

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Total Debit</p>
                <p class="text-2xl font-black font-mono text-blue-700">
                    {{ number_format($totalDebit ?? 0, 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Total Credit</p>
                <p class="text-2xl font-black font-mono text-purple-700">
                    {{ number_format($totalCredit ?? 0, 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Difference</p>
                <p class="text-2xl font-black font-mono {{ round(($difference ?? 0), 2) == 0 ? 'text-green-700' : 'text-red-700' }}">
                    {{ number_format($difference ?? 0, 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Accounts</p>
                <p class="text-2xl font-black font-mono">
                    {{ $trialRows?->count() ?? 0 }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Status</p>
                <p class="text-2xl font-black font-mono {{ round(($difference ?? 0), 2) == 0 ? 'text-green-700' : 'text-red-700' }}">
                    {{ round(($difference ?? 0), 2) == 0 ? 'Balanced' : 'Unbalanced' }}
                </p>
            </div>

        </div>
    </div>

    {{-- HORIZONTAL FILTERS --}}
    <div class="bg-white border border-slate-300 p-4 mb-6 overflow-x-auto">
        <div class="flex gap-3 min-w-max items-center">

            <input type="text"
                   wire:model.live.debounce.500ms="search"
                   placeholder="Search account, code, type..."
                   class="w-72 border border-slate-300 px-3 py-2 text-xs">

            <input type="date"
                   wire:model.live="date_from"
                   class="w-44 border border-slate-300 px-3 py-2 text-xs">

            <input type="date"
                   wire:model.live="date_to"
                   class="w-44 border border-slate-300 px-3 py-2 text-xs">

            <select wire:model.live="account_type"
                    class="w-48 border border-slate-300 px-3 py-2 text-xs bg-white">
                <option value="">All Account Types</option>
                <option value="asset">Assets</option>
                <option value="liability">Liabilities</option>
                <option value="equity">Equity</option>
                <option value="revenue">Revenue</option>
                <option value="income">Income</option>
                <option value="expense">Expenses</option>
            </select>

            <select wire:model.live="source_module"
                    class="w-48 border border-slate-300 px-3 py-2 text-xs bg-white">
                <option value="">All Modules</option>
                @foreach($sourceModules ?? [] as $module)
                    <option value="{{ $module }}">{{ strtoupper(str_replace('_', ' ', $module)) }}</option>
                @endforeach
            </select>

            <label class="w-36 flex items-center gap-2 text-xs font-bold bg-slate-50 border border-slate-300 px-3 py-2">
                <input type="checkbox" wire:model.live="hide_zero_balances">
                Hide Zero
            </label>

            <button type="button"
                    wire:click="clearFilters"
                    class="w-36 bg-white border border-slate-300 px-4 py-2 text-xs font-bold">
                Clear Filters
            </button>

        </div>
    </div>

    {{-- STATUS PANEL --}}
    <div class="mb-6 border border-slate-300 p-4 {{ round(($difference ?? 0), 2) == 0 ? 'bg-green-50' : 'bg-red-50' }}">
        <p class="text-xs font-black {{ round(($difference ?? 0), 2) == 0 ? 'text-green-800' : 'text-red-800' }}">
            {{ round(($difference ?? 0), 2) == 0
                ? 'Trial Balance agrees. Total Debit equals Total Credit.'
                : 'Trial Balance does not agree. Check unbalanced or incomplete postings in General Ledger.' }}
        </p>
    </div>

    {{-- TRIAL BALANCE REGISTER --}}
    <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">

        <div class="bg-slate-900 text-white px-4 py-3">
            <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Trial Balance Register</p>
            <h2 class="text-sm font-black">Accounts Summary From General Ledger</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="p-3 text-left">Code</th>
                        <th class="p-3 text-left">Account</th>
                        <th class="p-3 text-left">Type</th>
                        <th class="p-3 text-left">Category</th>
                        <th class="p-3 text-right">Debit Total</th>
                        <th class="p-3 text-right">Credit Total</th>
                        <th class="p-3 text-right">Debit Balance</th>
                        <th class="p-3 text-right">Credit Balance</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse($trialRows ?? [] as $row)
                        <tr>
                            <td class="p-3 font-mono font-bold text-green-700">
                                {{ $row->account_code ?? '-' }}
                            </td>

                            <td class="p-3 font-bold">
                                {{ $row->account_name }}
                            </td>

                            <td class="p-3 uppercase">
                                {{ $row->account_type ?? '-' }}
                            </td>

                            <td class="p-3">
                                {{ str_replace('_', ' ', $row->category ?? '-') }}
                            </td>

                            <td class="p-3 text-right font-mono text-blue-700">
                                {{ number_format((float)($row->debit_total ?? 0), 2) }}
                            </td>

                            <td class="p-3 text-right font-mono text-purple-700">
                                {{ number_format((float)($row->credit_total ?? 0), 2) }}
                            </td>

                            <td class="p-3 text-right font-mono font-black">
                                {{ number_format((float)($row->debit_balance ?? 0), 2) }}
                            </td>

                            <td class="p-3 text-right font-mono font-black">
                                {{ number_format((float)($row->credit_balance ?? 0), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-10 text-center text-slate-400 font-bold">
                                No trial balance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-slate-900 text-white font-black">
                    <tr>
                        <td colspan="6" class="p-3 text-right">TOTALS</td>
                        <td class="p-3 text-right font-mono">
                            {{ number_format($totalDebit ?? 0, 2) }}
                        </td>
                        <td class="p-3 text-right font-mono">
                            {{ number_format($totalCredit ?? 0, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>