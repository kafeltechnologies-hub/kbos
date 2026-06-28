<div class="min-h-screen bg-slate-100 p-6 text-slate-900">

    @include('livewire.finance._header', [
        'title' => 'Tax Centre',
        'subtitle' => 'Track VAT, NHIL, GETFund, WHT and other statutory tax postings from the General Ledger.'
    ])

    @include('livewire.finance._nav')

    @if(session()->has('success'))
        <div class="mb-4 border-l-4 border-green-600 bg-green-50 p-3 text-xs font-bold text-green-900">
            {{ session('success') }}
        </div>
    @endif

    {{-- KPI CARDS --}}
    <div class="w-full mb-6 overflow-x-auto">
        <div class="flex gap-4 min-w-max">

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">VAT Payable</p>
                <p class="text-2xl font-black font-mono text-red-700">{{ number_format($vatPayable ?? 0, 2) }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">NHIL Payable</p>
                <p class="text-2xl font-black font-mono text-orange-700">{{ number_format($nhilPayable ?? 0, 2) }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">GETFund Payable</p>
                <p class="text-2xl font-black font-mono text-amber-700">{{ number_format($getfundPayable ?? 0, 2) }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">WHT Payable</p>
                <p class="text-2xl font-black font-mono text-purple-700">{{ number_format($whtPayable ?? 0, 2) }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Total Tax Liability</p>
                <p class="text-2xl font-black font-mono text-red-800">{{ number_format($totalTaxLiability ?? 0, 2) }}</p>
            </div>

        </div>
    </div>

    {{-- HORIZONTAL FILTERS --}}
    <div class="bg-white border border-slate-300 p-4 mb-6 overflow-x-auto">
        <div class="flex gap-3 min-w-max items-center">

            <input type="text"
                   wire:model.live.debounce.500ms="search"
                   placeholder="Search tax, reference, narration..."
                   class="w-72 border border-slate-300 px-3 py-2 text-xs">

            <input type="date"
                   wire:model.live="date_from"
                   class="w-44 border border-slate-300 px-3 py-2 text-xs">

            <input type="date"
                   wire:model.live="date_to"
                   class="w-44 border border-slate-300 px-3 py-2 text-xs">

            <select wire:model.live="tax_type"
                    class="w-48 border border-slate-300 px-3 py-2 text-xs bg-white">
                <option value="">All Tax Types</option>
                <option value="vat">VAT</option>
                <option value="nhil">NHIL</option>
                <option value="getfund">GETFund</option>
                <option value="wht">Withholding Tax</option>
                <option value="paye">PAYE</option>
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

    {{-- TAX REGISTER --}}
    <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">

        <div class="bg-slate-900 text-white px-4 py-3">
            <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Tax Register</p>
            <h2 class="text-sm font-black">Tax-related General Ledger Entries</h2>
        </div>

        <div class="divide-y divide-slate-200">
            @forelse($taxRows ?? [] as $entry)
                <div class="p-4">

                    <div class="flex flex-col md:flex-row md:justify-between gap-4">

                        <div>
                            <div class="flex flex-wrap gap-2 items-center">
                                <p class="font-mono font-black text-green-700">{{ $entry->reference ?? '-' }}</p>

                                <span class="px-2 py-1 text-[10px] uppercase font-bold border bg-red-50 text-red-700 border-red-300">
                                    {{ $entry->tax_type ?? $entry->account_name ?? 'Tax' }}
                                </span>

                                <span class="px-2 py-1 text-[10px] uppercase font-bold border bg-slate-50 border-slate-300">
                                    {{ $entry->source_module ?? 'finance' }}
                                </span>
                            </div>

                            <p class="text-xs font-bold mt-1">
                                {{ $entry->account_code ?? '' }} {{ $entry->account_code ? '—' : '' }} {{ $entry->account_name ?? '-' }}
                            </p>

                            <p class="text-[10px] text-slate-500 mt-1">
                                {{ $entry->entry_date?->format('d M Y') ?? $entry->transaction_date?->format('d M Y') ?? '-' }}
                                |
                                {{ $entry->source_type ?? 'tax_posting' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">

                            <div class="bg-slate-50 border border-slate-200 px-3 py-2 min-w-28">
                                <p class="text-[10px] uppercase text-slate-500 font-bold">Debit</p>
                                <p class="font-mono font-black text-blue-700">{{ number_format((float)($entry->debit ?? 0), 2) }}</p>
                            </div>

                            <div class="bg-slate-50 border border-slate-200 px-3 py-2 min-w-28">
                                <p class="text-[10px] uppercase text-slate-500 font-bold">Credit</p>
                                <p class="font-mono font-black text-red-700">{{ number_format((float)($entry->credit ?? 0), 2) }}</p>
                            </div>

                            <div class="bg-slate-50 border border-slate-200 px-3 py-2 min-w-28">
                                <p class="text-[10px] uppercase text-slate-500 font-bold">Balance</p>
                                <p class="font-mono font-black text-purple-700">
                                    {{ number_format((float)(($entry->credit ?? 0) - ($entry->debit ?? 0)), 2) }}
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="mt-3 bg-slate-50 border border-slate-200 p-3">
                        <p class="text-[10px] uppercase text-slate-500 font-bold">Narration</p>
                        <p class="text-xs">{{ $entry->description ?? $entry->narration ?? '-' }}</p>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400 font-bold">
                    No tax entries found.
                </div>
            @endforelse
        </div>
    </div>
</div>