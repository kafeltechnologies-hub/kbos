<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

    <form id="journal-entry-form" wire:submit.prevent="save">

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-purple-900 px-4 py-3 flex items-center justify-between border-b border-slate-950">
                <div>
                    <span class="text-[10px] font-bold text-purple-200 tracking-wider uppercase font-mono block">
                        Accounting Control Area
                    </span>

                    <h1 class="text-sm font-bold text-white">
                        Journal Entries — Manual Adjustments, Opening Balances, Corrections & Accruals
                    </h1>
                </div>

                <div class="hidden sm:block border-l border-slate-600 pl-4 text-right">
                    <span class="text-[10px] block uppercase font-mono text-slate-300">
                        Journal Records
                    </span>

                    <span class="text-base font-black font-mono text-white">
                        {{ $journals->count() }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 bg-slate-50 px-3 py-2 border-b border-slate-200">

                <button type="button" wire:click="createNew()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Create New
                </button>

                <button type="submit"
                    class="px-4 py-1.5 text-xs font-semibold text-white bg-purple-700 border border-purple-800 hover:bg-purple-800 shadow-sm">
                    {{ $isEditing ? 'Update Journal' : 'Save Journal' }}
                </button>

                <button type="button" wire:click="addLine()"
                    class="px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50 border border-green-300 hover:bg-green-100 shadow-sm">
                    Add Line
                </button>

                <button type="button" wire:click="clearBuffer()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Clear Buffer
                </button>

                <button type="button" wire:click="sync()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Sync
                </button>

                <div class="ml-auto px-3 py-1 bg-purple-50 border border-purple-200 text-[11px] font-mono font-bold text-slate-700">
                    JOURNAL NO:
                    <span class="text-purple-700">
                        {{ $isEditing ? 'EDIT MODE' : $this->generateJournalNumber() }}
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

        @if (session()->has('error'))
            <div class="mt-4 border-l-4 border-red-600 bg-red-50 p-3 text-xs font-medium text-red-900 shadow-sm">
                {{ session('error') }}
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
                            01. Journal Header
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Journal Date
                            </label>

                            <input type="date" wire:model="journal_date"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-purple-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Status
                            </label>

                            <select wire:model="status"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-purple-600 focus:ring-0 outline-none">
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption }}">
                                        {{ strtoupper($statusOption) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Reference No.
                            </label>

                            <input type="text" wire:model="reference_no"
                                placeholder="Optional supporting document reference"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-purple-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Narration <span class="text-red-500">*</span>
                            </label>

                            <textarea wire:model="narration" rows="3"
                                placeholder="Explain why this journal is being posted..."
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-purple-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200 flex items-center justify-between">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            02. Journal Lines
                        </h2>

                        <button type="button" wire:click="addLine()"
                            class="px-2 py-1 text-[10px] font-bold bg-green-50 text-green-700 border border-green-300">
                            Add Line
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-slate-700 border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-3 min-w-[260px]">Account</th>
                                    <th class="px-3 py-3 min-w-[260px]">Description</th>
                                    <th class="px-3 py-3 w-36">Debit</th>
                                    <th class="px-3 py-3 w-36">Credit</th>
                                    <th class="px-3 py-3 w-20">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200">
                                @foreach($lines as $index => $line)
                                    <tr>
                                        <td class="px-3 py-3 align-top">
                                            <select wire:model.live="lines.{{ $index }}.account_id"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                                <option value="">Select account</option>

                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">
                                                        {{ $account->account_code }} — {{ $account->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <textarea wire:model.live="lines.{{ $index }}.description" rows="2"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5 resize-none"></textarea>
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input wire:model.live="lines.{{ $index }}.debit" type="number" step="0.01"
                                                class="w-full text-xs bg-white border border-green-300 px-2 py-1.5 focus:border-green-600 focus:ring-0 outline-none">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input wire:model.live="lines.{{ $index }}.credit" type="number" step="0.01"
                                                class="w-full text-xs bg-white border border-red-300 px-2 py-1.5 focus:border-red-600 focus:ring-0 outline-none">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <button type="button" wire:click="removeLine({{ $index }})"
                                                class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot class="bg-slate-100 font-bold">
                                <tr>
                                    <td colspan="2" class="px-3 py-3 text-right border-r border-slate-300">
                                        TOTAL
                                    </td>

                                    <td class="px-3 py-3 font-mono text-green-700">
                                        {{ number_format((float) $total_debit, 2) }}
                                    </td>

                                    <td class="px-3 py-3 font-mono text-red-700">
                                        {{ number_format((float) $total_credit, 2) }}
                                    </td>

                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            03. Approval
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Prepared By
                            </label>

                            <input type="text" wire:model="prepared_by"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-purple-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Approved By
                            </label>

                            <input type="text" wire:model="approved_by"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-purple-600 focus:ring-0 outline-none">
                        </div>

                    </div>
                </div>

            </div>

            <div class="xl:col-span-1">
                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden sticky top-6">
                    <div class="bg-slate-800 px-3 py-2 border-b border-slate-900">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-white">
                            Journal Snapshot
                        </h2>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="bg-green-50 border border-green-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-green-700">Total Debit</p>
                            <p class="mt-1 text-xl font-black font-mono text-green-900">
                                {{ number_format((float) $total_debit, 2) }}
                            </p>
                        </div>

                        <div class="bg-red-50 border border-red-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-red-700">Total Credit</p>
                            <p class="mt-1 text-xl font-black font-mono text-red-900">
                                {{ number_format((float) $total_credit, 2) }}
                            </p>
                        </div>

                        <div class="{{ abs((float) $difference) < 0.01 ? 'bg-blue-50 border-blue-200' : 'bg-amber-50 border-amber-200' }} border p-4">
                            <p class="text-[10px] font-bold uppercase {{ abs((float) $difference) < 0.01 ? 'text-blue-700' : 'text-amber-700' }}">
                                Difference
                            </p>
                            <p class="mt-1 text-xl font-black font-mono {{ abs((float) $difference) < 0.01 ? 'text-blue-900' : 'text-amber-900' }}">
                                {{ number_format((float) $difference, 2) }}
                            </p>
                        </div>

                        <div class="{{ abs((float) $difference) < 0.01 && (float) $total_debit > 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border p-4">
                            <p class="text-[10px] font-bold uppercase {{ abs((float) $difference) < 0.01 && (float) $total_debit > 0 ? 'text-green-700' : 'text-red-700' }}">
                                Posting Status
                            </p>
                            <p class="mt-2 text-xs leading-5 font-bold">
                                @if(abs((float) $difference) < 0.01 && (float) $total_debit > 0)
                                    Balanced and ready for posting.
                                @else
                                    Journal must balance before posting.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>

    <div class="mt-8 border border-slate-300 bg-white shadow-sm overflow-hidden">

        <div class="bg-slate-800 px-4 py-3 border-b border-slate-900">
            <h2 class="text-xs font-bold uppercase tracking-wider text-white">
                Journal Entry Ledger
            </h2>
        </div>

        <div class="w-full bg-slate-200 border-b border-slate-300 flex items-center shadow-inner">
            <span class="pl-4 text-slate-500 font-mono text-sm select-none">🔍</span>

            <input wire:model.live.debounce.500ms="search" type="text"
                placeholder="Filter journal entries..."
                class="w-full bg-transparent border-0 px-3 py-3 text-xs text-slate-900 placeholder-slate-500 focus:ring-0 outline-none font-medium">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap table-fixed">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300">
                    <tr>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Journal No.</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Date</th>
                        <th class="w-44 px-4 py-4 border-r border-slate-200">Reference</th>
                        <th class="w-72 px-4 py-4 border-r border-slate-200">Narration</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200 text-right">Debit</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200 text-right">Credit</th>
                        <th class="w-28 px-4 py-4 border-r border-slate-200">Status</th>
                        <th class="w-64 px-4 py-4">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($journals as $journal)
                        <tr class="hover:bg-purple-50/70 border-b border-slate-200 transition">
                            <td class="px-4 py-6 font-mono font-bold text-purple-800 border-r border-slate-200 bg-slate-50/50">
                                {{ $journal->journal_number }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono border-r border-slate-200">
                                {{ $journal->journal_date }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200 font-mono">
                                {{ $journal->reference_no ?? '-' }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="truncate">
                                    {{ $journal->narration }}
                                </div>

                                <div class="text-[10px] text-slate-400 mt-1">
                                    {{ $journal->lines->count() }} line(s)
                                </div>
                            </td>

                            <td class="px-4 py-6 text-right text-green-700 font-mono border-r border-slate-200">
                                {{ number_format((float) $journal->total_debit, 2) }}
                            </td>

                            <td class="px-4 py-6 text-right text-red-700 font-mono border-r border-slate-200">
                                {{ number_format((float) $journal->total_credit, 2) }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <span class="px-2 py-1 text-[10px] font-bold uppercase border
                                    @if($journal->status === 'posted' || $journal->status === 'approved')
                                        bg-green-50 text-green-700 border-green-300
                                    @elseif($journal->status === 'cancelled')
                                        bg-red-50 text-red-700 border-red-300
                                    @elseif($journal->status === 'draft')
                                        bg-amber-50 text-amber-700 border-amber-300
                                    @else
                                        bg-slate-50 text-slate-700 border-slate-300
                                    @endif">
                                    {{ strtoupper($journal->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-6">
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="editJournal({{ $journal->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">
                                        Edit
                                    </button>

                                    <button type="button" wire:click="approveJournal({{ $journal->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-green-50 text-green-700 border border-green-300">
                                        Approve
                                    </button>

                                    <button type="button" wire:click="postJournal({{ $journal->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-300">
                                        Post GL
                                    </button>

                                    <button type="button" wire:click="cancelJournal({{ $journal->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                        Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                [Err] 0 journal entries returned.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>