<div class="min-h-screen bg-slate-100 p-6 text-slate-900">

    @include('livewire.finance._header', [
        'title' => 'Cash Flow Statement',
        'subtitle' => 'Cash movement statement generated from synchronized General Ledger postings.'
    ])

    @include('livewire.finance._nav')

    {{-- HORIZONTAL KPI CARDS --}}
    <div class="w-full mb-6 overflow-x-auto">
        <div class="flex gap-4 min-w-max">

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Opening Cash</p>
                <p class="text-2xl font-black font-mono text-blue-700">
                    {{ number_format($openingCash ?? 0, 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Cash Inflows</p>
                <p class="text-2xl font-black font-mono text-green-700">
                    {{ number_format($totalInflows ?? 0, 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Cash Outflows</p>
                <p class="text-2xl font-black font-mono text-red-700">
                    {{ number_format($totalOutflows ?? 0, 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Net Cash Flow</p>
                <p class="text-2xl font-black font-mono {{ ($netCashFlow ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                    {{ number_format($netCashFlow ?? 0, 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Closing Cash</p>
                <p class="text-2xl font-black font-mono text-purple-700">
                    {{ number_format($closingCash ?? 0, 2) }}
                </p>
            </div>

        </div>
    </div>

    {{-- HORIZONTAL FILTERS --}}
    <div class="bg-white border border-slate-300 p-4 mb-6 overflow-x-auto">
        <div class="flex gap-3 min-w-max items-center">

            <input type="text"
                   wire:model.live.debounce.500ms="search"
                   placeholder="Search reference, narration, account..."
                   class="w-72 border border-slate-300 px-3 py-2 text-xs">

            <input type="date"
                   wire:model.live="date_from"
                   class="w-44 border border-slate-300 px-3 py-2 text-xs">

            <input type="date"
                   wire:model.live="date_to"
                   class="w-44 border border-slate-300 px-3 py-2 text-xs">

            <select wire:model.live="cash_account"
                    class="w-56 border border-slate-300 px-3 py-2 text-xs bg-white">
                <option value="">All Cash/Bank Accounts</option>
                @foreach($cashAccounts ?? [] as $account)
                    <option value="{{ $account->account_name }}">
                        {{ $account->account_code ?? '' }} {{ $account->account_code ? '—' : '' }} {{ $account->account_name }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="source_module"
                    class="w-48 border border-slate-300 px-3 py-2 text-xs bg-white">
                <option value="">All Modules</option>
                @foreach($sourceModules ?? [] as $module)
                    <option value="{{ $module }}">{{ strtoupper(str_replace('_', ' ', $module)) }}</option>
                @endforeach
            </select>

            <button type="button"
                    wire:click="clearFilters"
                    class="w-36 bg-white border border-slate-300 px-4 py-2 text-xs font-bold">
                Clear Filters
            </button>

        </div>
    </div>

    {{-- CASH FLOW SECTIONS --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">

        {{-- OPERATING --}}
        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Operating Activities</p>
                <h2 class="text-sm font-black">Core Business Cash Flow</h2>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse($operatingRows ?? [] as $row)
                    <div class="p-4 flex justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold">{{ $row->label ?? $row->account_name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $row->source_module ?? 'GL' }}</p>
                        </div>
                        <p class="font-mono font-black {{ ($row->amount ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            {{ number_format($row->amount ?? 0, 2) }}
                        </p>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 font-bold">
                        No operating cash flow records.
                    </div>
                @endforelse
            </div>

            <div class="p-4 bg-slate-900 text-white flex justify-between font-black text-sm">
                <span>Net Operating Cash</span>
                <span class="font-mono">{{ number_format($netOperatingCash ?? 0, 2) }}</span>
            </div>
        </div>

        {{-- INVESTING --}}
        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold">Investing Activities</p>
                <h2 class="text-sm font-black">Assets & Investments</h2>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse($investingRows ?? [] as $row)
                    <div class="p-4 flex justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold">{{ $row->label ?? $row->account_name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $row->source_module ?? 'GL' }}</p>
                        </div>
                        <p class="font-mono font-black {{ ($row->amount ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            {{ number_format($row->amount ?? 0, 2) }}
                        </p>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 font-bold">
                        No investing cash flow records.
                    </div>
                @endforelse
            </div>

            <div class="p-4 bg-slate-900 text-white flex justify-between font-black text-sm">
                <span>Net Investing Cash</span>
                <span class="font-mono">{{ number_format($netInvestingCash ?? 0, 2) }}</span>
            </div>
        </div>

        {{-- FINANCING --}}
        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-purple-300 font-bold">Financing Activities</p>
                <h2 class="text-sm font-black">Capital & Loans</h2>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse($financingRows ?? [] as $row)
                    <div class="p-4 flex justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold">{{ $row->label ?? $row->account_name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $row->source_module ?? 'GL' }}</p>
                        </div>
                        <p class="font-mono font-black {{ ($row->amount ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            {{ number_format($row->amount ?? 0, 2) }}
                        </p>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 font-bold">
                        No financing cash flow records.
                    </div>
                @endforelse
            </div>

            <div class="p-4 bg-slate-900 text-white flex justify-between font-black text-sm">
                <span>Net Financing Cash</span>
                <span class="font-mono">{{ number_format($netFinancingCash ?? 0, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- CASH MOVEMENT REGISTER --}}
    <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">

        <div class="bg-slate-900 text-white px-4 py-3">
            <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Cash Movement Register</p>
            <h2 class="text-sm font-black">Detailed Cash/Bank Ledger Entries</h2>
        </div>

        <div class="divide-y divide-slate-200">
            @forelse($cashRows ?? [] as $entry)
                <div class="p-4">

                    <div class="flex flex-col md:flex-row md:justify-between gap-4">

                        <div>
                            <div class="flex flex-wrap gap-2 items-center">
                                <p class="font-mono font-black text-green-700">
                                    {{ $entry->reference ?? '-' }}
                                </p>

                                <span class="px-2 py-1 text-[10px] uppercase font-bold border bg-slate-50 border-slate-300">
                                    {{ $entry->source_module ?? 'finance' }}
                                </span>

                                <span class="px-2 py-1 text-[10px] uppercase font-bold border bg-blue-50 text-blue-700 border-blue-300">
                                    {{ $entry->source_type ?? 'cash' }}
                                </span>
                            </div>

                            <p class="text-xs font-bold mt-1">
                                {{ $entry->account_code ?? '' }}
                                {{ $entry->account_code ? '—' : '' }}
                                {{ $entry->account_name ?? $entry->account ?? '-' }}
                            </p>

                            <p class="text-[10px] text-slate-500 mt-1">
                                {{ $entry->entry_date?->format('d M Y') ?? $entry->transaction_date?->format('d M Y') }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <div class="bg-slate-50 border border-slate-200 px-3 py-2 min-w-28">
                                <p class="text-[10px] uppercase text-slate-500 font-bold">Inflow</p>
                                <p class="font-mono font-black text-green-700">
                                    {{ number_format((float)($entry->inflow ?? 0), 2) }}
                                </p>
                            </div>

                            <div class="bg-slate-50 border border-slate-200 px-3 py-2 min-w-28">
                                <p class="text-[10px] uppercase text-slate-500 font-bold">Outflow</p>
                                <p class="font-mono font-black text-red-700">
                                    {{ number_format((float)($entry->outflow ?? 0), 2) }}
                                </p>
                            </div>

                            <div class="bg-slate-50 border border-slate-200 px-3 py-2 min-w-28">
                                <p class="text-[10px] uppercase text-slate-500 font-bold">Net</p>
                                <p class="font-mono font-black {{ (($entry->net ?? 0) >= 0) ? 'text-green-700' : 'text-red-700' }}">
                                    {{ number_format((float)($entry->net ?? 0), 2) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 bg-slate-50 border border-slate-200 p-3">
                        <p class="text-[10px] uppercase text-slate-500 font-bold">Narration</p>
                        <p class="text-xs">
                            {{ $entry->description ?? $entry->narration ?? '-' }}
                        </p>
                    </div>

                </div>
            @empty
                <div class="p-10 text-center text-slate-400 font-bold">
                    No cash flow entries found.
                </div>
            @endforelse
        </div>
    </div>
</div>