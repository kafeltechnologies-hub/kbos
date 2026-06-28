<div class="min-h-screen bg-slate-100 p-6 text-slate-900">

    @include('livewire.finance._header', [
        'title' => 'Profit & Loss Statement',
        'subtitle' => 'Revenue, cost of sales, expenses and net profitability from posted general ledger entries.'
    ])

    @include('livewire.finance._nav')

    <div class="bg-white border border-slate-300 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="text-[10px] uppercase font-bold text-slate-500">From</label>
                <input type="date" wire:model.live="date_from" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
            </div>

            <div>
                <label class="text-[10px] uppercase font-bold text-slate-500">To</label>
                <input type="date" wire:model.live="date_to" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
            </div>

            <div>
                <label class="text-[10px] uppercase font-bold text-slate-500">Account Type</label>
                <select wire:model.live="account_type_filter" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                    <option value="">All P&L Types</option>
                    <option value="Income">Income</option>
                    <option value="Revenue">Revenue</option>
                    <option value="Sales">Sales</option>
                    <option value="COGS">COGS</option>
                    <option value="Cost of Sales">Cost of Sales</option>
                    <option value="Expense">Expense</option>
                    <option value="Expenses">Expenses</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] uppercase font-bold text-slate-500">Search</label>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search account..." class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
            </div>

            <div class="flex items-end">
                <button type="button" wire:click="resetFilters" class="w-full bg-slate-900 text-white border border-slate-950 px-3 py-2 text-xs font-bold">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="bg-slate-950 text-white border border-slate-800 p-4 mb-6">
        <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Reporting Period</p>
        <h2 class="text-sm font-black">{{ $periodLabel }}</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">

        <div class="bg-white border border-slate-300 p-5">
            <p class="text-[10px] uppercase font-bold text-slate-500">Revenue</p>
            <p class="text-2xl font-black font-mono text-green-700">{{ number_format($totalRevenue ?? 0, 2) }}</p>
        </div>

        <div class="bg-white border border-slate-300 p-5">
            <p class="text-[10px] uppercase font-bold text-slate-500">COGS</p>
            <p class="text-2xl font-black font-mono text-red-700">{{ number_format($totalCogs ?? 0, 2) }}</p>
        </div>

        <div class="bg-white border border-slate-300 p-5">
            <p class="text-[10px] uppercase font-bold text-slate-500">Gross Profit</p>
            <p class="text-2xl font-black font-mono {{ ($grossProfit ?? 0) >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                {{ number_format($grossProfit ?? 0, 2) }}
            </p>
        </div>

        <div class="bg-white border border-slate-300 p-5">
            <p class="text-[10px] uppercase font-bold text-slate-500">Expenses</p>
            <p class="text-2xl font-black font-mono text-amber-700">{{ number_format($totalExpenses ?? 0, 2) }}</p>
        </div>

        <div class="bg-white border border-slate-300 p-5">
            <p class="text-[10px] uppercase font-bold text-slate-500">Net Profit</p>
            <p class="text-2xl font-black font-mono {{ ($netProfit ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                {{ number_format($netProfit ?? 0, 2) }}
            </p>
        </div>

        <div class="bg-white border border-slate-300 p-5">
            <p class="text-[10px] uppercase font-bold text-slate-500">Net Margin</p>
            <p class="text-2xl font-black font-mono {{ ($netProfitMargin ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                {{ number_format($netProfitMargin ?? 0, 2) }}%
            </p>
        </div>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <div class="xl:col-span-2 bg-white border border-slate-300 shadow-sm overflow-hidden">

            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Statement</p>
                <h2 class="text-sm font-black">Profit & Loss Breakdown</h2>
            </div>

            <div class="divide-y divide-slate-300">

                <div class="p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xs font-black uppercase text-green-700">Revenue</h3>
                        <p class="font-mono font-black text-green-700">{{ number_format($totalRevenue ?? 0, 2) }}</p>
                    </div>

                    <table class="w-full text-xs">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="p-3 text-left">Code</th>
                                <th class="p-3 text-left">Account</th>
                                <th class="p-3 text-right">Debit</th>
                                <th class="p-3 text-right">Credit</th>
                                <th class="p-3 text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($revenueAccounts ?? [] as $account)
                                <tr>
                                    <td class="p-3 font-mono">{{ $account->account_code }}</td>
                                    <td class="p-3 font-bold">{{ $account->account_name }}</td>
                                    <td class="p-3 text-right font-mono">{{ number_format($account->debit_total ?? 0, 2) }}</td>
                                    <td class="p-3 text-right font-mono">{{ number_format($account->credit_total ?? 0, 2) }}</td>
                                    <td class="p-3 text-right font-mono font-bold">{{ number_format($account->balance ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400 font-bold">No revenue accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xs font-black uppercase text-red-700">Cost of Goods Sold / Cost of Sales</h3>
                        <p class="font-mono font-black text-red-700">{{ number_format($totalCogs ?? 0, 2) }}</p>
                    </div>

                    <table class="w-full text-xs">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="p-3 text-left">Code</th>
                                <th class="p-3 text-left">Account</th>
                                <th class="p-3 text-right">Debit</th>
                                <th class="p-3 text-right">Credit</th>
                                <th class="p-3 text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($cogsAccounts ?? [] as $account)
                                <tr>
                                    <td class="p-3 font-mono">{{ $account->account_code }}</td>
                                    <td class="p-3 font-bold">{{ $account->account_name }}</td>
                                    <td class="p-3 text-right font-mono">{{ number_format($account->debit_total ?? 0, 2) }}</td>
                                    <td class="p-3 text-right font-mono">{{ number_format($account->credit_total ?? 0, 2) }}</td>
                                    <td class="p-3 text-right font-mono font-bold">{{ number_format($account->balance ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400 font-bold">No COGS accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-blue-50 p-5 flex justify-between items-center">
                    <p class="text-sm font-black uppercase text-blue-900">Gross Profit</p>
                    <p class="text-xl font-black font-mono {{ ($grossProfit ?? 0) >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                        {{ number_format($grossProfit ?? 0, 2) }}
                    </p>
                </div>

                <div class="p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xs font-black uppercase text-amber-700">Operating Expenses</h3>
                        <p class="font-mono font-black text-amber-700">{{ number_format($totalExpenses ?? 0, 2) }}</p>
                    </div>

                    <table class="w-full text-xs">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="p-3 text-left">Code</th>
                                <th class="p-3 text-left">Account</th>
                                <th class="p-3 text-right">Debit</th>
                                <th class="p-3 text-right">Credit</th>
                                <th class="p-3 text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($expenseAccounts ?? [] as $account)
                                <tr>
                                    <td class="p-3 font-mono">{{ $account->account_code }}</td>
                                    <td class="p-3 font-bold">{{ $account->account_name }}</td>
                                    <td class="p-3 text-right font-mono">{{ number_format($account->debit_total ?? 0, 2) }}</td>
                                    <td class="p-3 text-right font-mono">{{ number_format($account->credit_total ?? 0, 2) }}</td>
                                    <td class="p-3 text-right font-mono font-bold">{{ number_format($account->balance ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400 font-bold">No expense accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-slate-950 text-white p-5 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Bottom Line</p>
                        <p class="text-lg font-black uppercase">Net Profit / Loss</p>
                    </div>

                    <p class="text-3xl font-black font-mono {{ ($netProfit ?? 0) >= 0 ? 'text-green-300' : 'text-red-300' }}">
                        {{ number_format($netProfit ?? 0, 2) }}
                    </p>
                </div>

            </div>

        </div>

        <div class="space-y-6">

            <div class="bg-white border border-slate-300 p-5">
                <p class="text-[10px] uppercase font-bold text-slate-500">Gross Profit Margin</p>
                <p class="text-4xl font-black font-mono text-blue-700">{{ number_format($grossProfitMargin ?? 0, 2) }}%</p>
                <p class="text-xs text-slate-500 mt-2">Gross profit divided by revenue.</p>
            </div>

            <div class="bg-white border border-slate-300 p-5">
                <p class="text-[10px] uppercase font-bold text-slate-500">Net Profit Margin</p>
                <p class="text-4xl font-black font-mono {{ ($netProfitMargin ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                    {{ number_format($netProfitMargin ?? 0, 2) }}%
                </p>
                <p class="text-xs text-slate-500 mt-2">Net profit divided by revenue.</p>
            </div>

            <div class="bg-white border border-slate-300 overflow-hidden">
                <div class="bg-slate-900 text-white px-4 py-3">
                    <p class="text-xs font-black uppercase">Recent P&L Postings</p>
                </div>

                <div class="divide-y divide-slate-200">
                    @forelse($recentEntries ?? [] as $entry)
                        <div class="p-4">
                            <p class="text-xs font-black text-slate-800">
                                {{ $entry->account?->account_name ?? 'No Account' }}
                            </p>
                            <p class="text-[10px] text-slate-500">
                                {{ $entry->posting_date?->format('d M Y') ?? '-' }}
                                |
                                {{ $entry->reference_no ?? '-' }}
                            </p>
                            <div class="mt-2 flex justify-between text-xs font-mono">
                                <span class="text-blue-700">Dr {{ number_format($entry->debit ?? 0, 2) }}</span>
                                <span class="text-purple-700">Cr {{ number_format($entry->credit ?? 0, 2) }}</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                {{ $entry->description ?? '-' }}
                            </p>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 font-bold">
                            No recent P&L postings.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>