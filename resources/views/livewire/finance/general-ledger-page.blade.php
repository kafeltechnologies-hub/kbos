<div class="min-h-screen bg-slate-100 p-6 text-slate-900">

@include('livewire.finance._header', [
    'title' => 'General Ledger Posting Centre',
    'subtitle' => 'Accountant workspace for reviewing approved operations, assigning ledger accounts, posting journals, and viewing GL entries.'
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

<div class="w-full mb-6 overflow-x-auto">
    <div class="flex gap-4 min-w-max">
        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Pending Invoices</p>
            <p class="text-2xl font-black font-mono text-blue-700">{{ count($pendingDocuments ?? []) }}</p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Pending Receipts/Payments</p>
            <p class="text-2xl font-black font-mono text-purple-700">{{ count($pendingPayments ?? []) }}</p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Filtered Debit</p>
            <p class="text-2xl font-black font-mono text-green-700">{{ number_format($totalDebit ?? 0, 2) }}</p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Filtered Credit</p>
            <p class="text-2xl font-black font-mono text-red-700">{{ number_format($totalCredit ?? 0, 2) }}</p>
        </div>
    </div>
</div>

<div class="bg-white border border-slate-300 mb-6 overflow-x-auto">
    <div class="flex gap-2 p-3 bg-slate-100 min-w-max">
        @foreach([
            'pending' => 'Pending Operations',
            'posting' => 'Posting Form',
            'ledger' => 'General Ledger Entries',
        ] as $tab => $label)
            <button type="button"
                    wire:click="go('{{ $tab }}')"
                    class="px-4 py-2 text-xs font-bold border {{ $activeTab === $tab ? 'bg-green-700 text-white border-green-800' : 'bg-white border-slate-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>

@if($activeTab === 'pending')
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold">Approved / Submitted Invoices</p>
                <h2 class="text-sm font-black">Invoice Documents Ready for GL Posting</h2>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse($pendingDocuments ?? [] as $doc)
                    <div class="p-4 flex flex-col xl:flex-row xl:justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap gap-2 items-center">
                                <p class="font-mono font-black text-blue-700">{{ $doc->document_no }}</p>
                                <span class="px-2 py-1 text-[10px] uppercase font-bold border bg-blue-50 text-blue-700 border-blue-300">
                                    {{ $doc->status }}
                                </span>
                            </div>

                            <p class="text-xs font-bold mt-1">{{ $doc->customer_name }}</p>
                            <p class="text-[10px] text-slate-500 mt-1">
                                {{ $doc->document_date?->format('d M Y') ?? '-' }}
                                @if($doc->project)
                                    | {{ $doc->project->project_name }}
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2 items-start">
                            <div class="bg-slate-50 border border-slate-200 px-3 py-2 min-w-32">
                                <p class="text-[10px] uppercase font-bold text-slate-500">Amount</p>
                                <p class="font-mono font-black">{{ number_format((float)$doc->grand_total, 2) }}</p>
                            </div>

                            <button type="button"
                                    wire:click="loadDocument({{ $doc->id }})"
                                    class="bg-green-700 text-white px-3 py-2 text-[10px] font-bold">
                                Prepare Posting
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 font-bold">
                        No invoice documents waiting for posting.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-purple-300 font-bold">Approved / Submitted Cash Operations</p>
                <h2 class="text-sm font-black">Receipts, Payments and Transfers Ready for GL Posting</h2>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse($pendingPayments ?? [] as $pay)
                    <div class="p-4 flex flex-col xl:flex-row xl:justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap gap-2 items-center">
                                <p class="font-mono font-black text-purple-700">{{ $pay->payment_no }}</p>
                                <span class="px-2 py-1 text-[10px] uppercase font-bold border bg-slate-50 border-slate-300">
                                    {{ $pay->payment_type }}
                                </span>
                                <span class="px-2 py-1 text-[10px] uppercase font-bold border bg-blue-50 text-blue-700 border-blue-300">
                                    {{ $pay->status }}
                                </span>
                            </div>

                            <p class="text-xs font-bold mt-1">{{ $pay->party_name }}</p>
                            <p class="text-[10px] text-slate-500 mt-1">
                                {{ $pay->payment_date?->format('d M Y') ?? '-' }}
                                @if($pay->project)
                                    | {{ $pay->project->project_name }}
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2 items-start">
                            <div class="bg-slate-50 border border-slate-200 px-3 py-2 min-w-32">
                                <p class="text-[10px] uppercase font-bold text-slate-500">Amount</p>
                                <p class="font-mono font-black">{{ number_format((float)$pay->gross_amount, 2) }}</p>
                            </div>

                            <button type="button"
                                    wire:click="loadPayment({{ $pay->id }})"
                                    class="bg-green-700 text-white px-3 py-2 text-[10px] font-bold">
                                Prepare Posting
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 font-bold">
                        No receipts, payments or transfers waiting for posting.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endif

@if($activeTab === 'posting')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <div class="xl:col-span-2 bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Journal Posting</p>
                <h2 class="text-sm font-black">Assign Accounts and Post Double Entry</h2>
            </div>

            <form wire:submit.prevent="postJournal" class="p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Date</label>
                        <input type="date" wire:model="posting_date" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Reference</label>
                        <input type="text" wire:model="posting_reference" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Source Type</label>
                        <input type="text" wire:model="source_type" readonly class="mt-1 w-full border border-slate-300 bg-slate-100 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Project</label>
                        <select wire:model="posting_project_id" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">No Project</option>
                            @foreach($projects ?? [] as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-500">Narration</label>
                    <textarea wire:model="posting_narration" rows="3" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs resize-none"></textarea>
                </div>

                <div class="border border-slate-300 overflow-hidden">
                    <div class="bg-slate-100 px-3 py-2 flex justify-between items-center">
                        <p class="text-xs font-black">Journal Lines</p>
                        <button type="button" wire:click="addLine" class="text-[10px] font-bold text-green-700">
                            + Add Line
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-[10px]">
                                <tr>
                                    <th class="text-left px-3 py-2 border-b">Account</th>
                                    <th class="text-left px-3 py-2 border-b">Description</th>
                                    <th class="text-right px-3 py-2 border-b">Debit</th>
                                    <th class="text-right px-3 py-2 border-b">Credit</th>
                                    <th class="text-center px-3 py-2 border-b">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($journalLines as $index => $line)
                                    <tr class="border-b">
                                        <td class="px-3 py-2 min-w-72">
                                            <select wire:model.live="journalLines.{{ $index }}.account_id" class="w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                                                <option value="">Select account</option>
                                                @foreach($accounts ?? [] as $account)
                                                    <option value="{{ $account->id }}">
                                                        {{ $account->account_code }} — {{ $account->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td class="px-3 py-2 min-w-72">
                                            <input type="text"
                                                   wire:model="journalLines.{{ $index }}.description"
                                                   class="w-full border border-slate-300 px-2 py-2 text-xs">
                                        </td>

                                        <td class="px-3 py-2 min-w-36">
                                            <input type="number"
                                                   step="0.01"
                                                   wire:model.live="journalLines.{{ $index }}.debit"
                                                   class="w-full border border-slate-300 px-2 py-2 text-xs text-right">
                                        </td>

                                        <td class="px-3 py-2 min-w-36">
                                            <input type="number"
                                                   step="0.01"
                                                   wire:model.live="journalLines.{{ $index }}.credit"
                                                   class="w-full border border-slate-300 px-2 py-2 text-xs text-right">
                                        </td>

                                        <td class="px-3 py-2 text-center">
                                            <button type="button"
                                                    wire:click="removeLine({{ $index }})"
                                                    class="text-red-700 text-[10px] font-bold">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot class="bg-slate-100 font-black">
                                <tr>
                                    <td colspan="2" class="px-3 py-3 text-right">TOTAL</td>
                                    <td class="px-3 py-3 text-right font-mono">{{ number_format($journalDebit ?? 0, 2) }}</td>
                                    <td class="px-3 py-3 text-right font-mono">{{ number_format($journalCredit ?? 0, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="bg-green-700 text-white px-4 py-2 text-xs font-bold">
                        Post to General Ledger
                    </button>

                    <button type="button" wire:click="clearPostingForm" class="bg-white border border-slate-300 px-4 py-2 text-xs font-bold">
                        Clear Posting
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <h2 class="text-sm font-black">Posting Control</h2>
            </div>

            <div class="p-5 space-y-4">
                <div class="border border-slate-300 p-4 bg-slate-50">
                    <p class="text-[10px] uppercase font-bold text-slate-500">Reference</p>
                    <p class="font-mono font-black text-green-700">{{ $posting_reference ?: '-' }}</p>
                </div>

                <div class="border border-slate-300 p-4 bg-slate-50">
                    <p class="text-[10px] uppercase font-bold text-slate-500">Debit Total</p>
                    <p class="font-mono font-black text-blue-700">{{ number_format($journalDebit ?? 0, 2) }}</p>
                </div>

                <div class="border border-slate-300 p-4 bg-slate-50">
                    <p class="text-[10px] uppercase font-bold text-slate-500">Credit Total</p>
                    <p class="font-mono font-black text-red-700">{{ number_format($journalCredit ?? 0, 2) }}</p>
                </div>

                <div class="border {{ round(($journalDebit ?? 0) - ($journalCredit ?? 0), 2) == 0 ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50' }} p-4">
                    <p class="text-[10px] uppercase font-bold">Difference</p>
                    <p class="font-mono font-black">
                        {{ number_format(round(($journalDebit ?? 0) - ($journalCredit ?? 0), 2), 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif

@if($activeTab === 'ledger')
    <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
        <div class="bg-slate-900 text-white px-4 py-3">
            <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">General Ledger</p>
            <h2 class="text-sm font-black">Posted Ledger Entries</h2>
        </div>

        <div class="bg-slate-50 border-b border-slate-300 p-4 overflow-x-auto">
            <div class="flex gap-3 min-w-max">
                <input type="text"
                       wire:model.live.debounce.500ms="search"
                       placeholder="Search reference, account, narration..."
                       class="w-72 border border-slate-300 px-3 py-2 text-xs">

                <input type="date" wire:model.live="date_from" class="w-44 border border-slate-300 px-3 py-2 text-xs">

                <input type="date" wire:model.live="date_to" class="w-44 border border-slate-300 px-3 py-2 text-xs">

                <select wire:model.live="source_filter" class="w-48 border border-slate-300 px-3 py-2 text-xs bg-white">
                    <option value="">All Sources</option>
                    <option value="invoice">Invoice</option>
                    <option value="receipt">Receipt</option>
                    <option value="payment">Payment</option>
                    <option value="transfer">Transfer</option>
                    <option value="manual_journal">Manual Journal</option>
                    <option value="fixed_assets">Fixed Assets</option>
                    <option value="materials">Materials</option>
                </select>

                <select wire:model.live="account_name" class="w-64 border border-slate-300 px-3 py-2 text-xs bg-white">
                    <option value="">All Accounts</option>
                    @foreach($accounts ?? [] as $account)
                        <option value="{{ $account->account_name }}">
                            {{ $account->account_code }} — {{ $account->account_name }}
                        </option>
                    @endforeach
                </select>

                <select wire:model.live="project_id" class="w-64 border border-slate-300 px-3 py-2 text-xs bg-white">
                    <option value="">All Projects</option>
                    @foreach($projects ?? [] as $project)
                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                    @endforeach
                </select>

                <button type="button" wire:click="clearFilters" class="bg-white border border-slate-300 px-4 py-2 text-xs font-bold">
                    Clear
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-100 text-slate-500 uppercase text-[10px]">
                    <tr>
                        <th class="px-3 py-2 text-left border-b">Date</th>
                        <th class="px-3 py-2 text-left border-b">Reference</th>
                        <th class="px-3 py-2 text-left border-b">Source</th>
                        <th class="px-3 py-2 text-left border-b">Account</th>
                        <th class="px-3 py-2 text-left border-b">Description</th>
                        <th class="px-3 py-2 text-right border-b">Debit</th>
                        <th class="px-3 py-2 text-right border-b">Credit</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($entries ?? [] as $entry)
                        <tr class="border-b hover:bg-slate-50">
                            <td class="px-3 py-2 whitespace-nowrap">
                                {{ $entry->entry_date?->format('d M Y') ?? $entry->posting_date?->format('d M Y') ?? '-' }}
                            </td>

                            <td class="px-3 py-2 font-mono font-bold text-green-700">
                                {{ $entry->reference ?? $entry->reference_no ?? '-' }}
                            </td>

                            <td class="px-3 py-2">
                                {{ $entry->source_type ?? $entry->reference_type ?? '-' }}
                            </td>

                            <td class="px-3 py-2">
                                <span class="font-mono">{{ $entry->account_code ?? '' }}</span>
                                {{ $entry->account_name ?? $entry->account?->account_name ?? '-' }}
                            </td>

                            <td class="px-3 py-2">
                                {{ $entry->description ?? $entry->narration ?? '-' }}
                            </td>

                            <td class="px-3 py-2 text-right font-mono text-blue-700">
                                {{ number_format((float)($entry->debit ?? 0), 2) }}
                            </td>

                            <td class="px-3 py-2 text-right font-mono text-red-700">
                                {{ number_format((float)($entry->credit ?? 0), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-10 text-center text-slate-400 font-bold">
                                No ledger entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-slate-100 font-black">
                    <tr>
                        <td colspan="5" class="px-3 py-3 text-right">TOTAL</td>
                        <td class="px-3 py-3 text-right font-mono text-blue-700">{{ number_format($totalDebit ?? 0, 2) }}</td>
                        <td class="px-3 py-3 text-right font-mono text-red-700">{{ number_format($totalCredit ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif

</div>