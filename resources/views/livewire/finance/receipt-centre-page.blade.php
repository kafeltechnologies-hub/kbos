<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

    <form id="receipt-centre-form" wire:submit.prevent="postLedger">

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-blue-900 px-4 py-3 flex items-center justify-between border-b border-slate-950">
                <div>
                    <span class="text-[10px] font-bold text-blue-200 tracking-wider uppercase font-mono block">
                        Finance Control Area
                    </span>

                    <h1 class="text-sm font-bold text-white">
                        Receipt Centre — Project Receipts, Income, Capital, Loans & Other Receipts
                    </h1>
                </div>

                <div class="hidden sm:block border-l border-slate-600 pl-4 text-right">
                    <span class="text-[10px] block uppercase font-mono text-slate-300">
                        Receipt Records
                    </span>

                    <span class="text-base font-black font-mono text-white">
                        {{ $receipts->count() }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 bg-slate-50 px-3 py-2 border-b border-slate-200">

                <button type="button" wire:click="createNew()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Create New
                </button>

                <button type="submit"
                    class="px-4 py-1.5 text-xs font-semibold text-white bg-blue-700 border border-blue-800 hover:bg-blue-800 shadow-sm">
                    {{ $isEditing ? 'Update Receipt' : 'Post Receipt' }}
                </button>

                <button type="button" wire:click="clearBuffer()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Clear Buffer
                </button>

                <button type="button" wire:click="sync()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Sync
                </button>

                <div class="ml-auto px-3 py-1 bg-blue-50 border border-blue-200 text-[11px] font-mono font-bold text-slate-700">
                    RECEIPT NO:
                    <span class="text-blue-700">
                        {{ $isEditing ? 'EDIT MODE' : $this->generateReceiptNumber() }}
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
                            01. Receipt Header
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Receipt Date
                            </label>

                            <input type="date" wire:model="receipt_date"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Status
                            </label>

                            <select wire:model="status"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption }}">{{ strtoupper($statusOption) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Receipt Type
                            </label>

                            <select wire:model.live="receipt_type"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                                @foreach($receiptTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            02. Receipt Classification
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        @if($receipt_type === 'project_receipt')
                            <div class="flex items-center w-full xl:col-span-2">
                                <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                    Project <span class="text-red-500">*</span>
                                </label>

                                <select wire:model.live="project_id"
                                    class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                                    <option value="">Select project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">
                                            {{ $project->project_code }} — {{ $project->project_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="flex items-center w-full xl:col-span-2">
                                <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                    Income Category <span class="text-red-500">*</span>
                                </label>

                                <select wire:model.live="income_category_id"
                                    class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                                    <option value="">Select matching category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->category_code }} — {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Payer <span class="text-red-500">*</span>
                            </label>

                            <input type="text" wire:model="payer_name"
                                placeholder="Client, customer, bank, director..."
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Receipt Method <span class="text-red-500">*</span>
                            </label>

                            <select wire:model="receipt_method"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                                <option value="">Select method</option>
                                @foreach($receiptMethods as $method)
                                    <option value="{{ $method }}">{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Reference No.
                            </label>

                            <input type="text" wire:model="reference_no"
                                placeholder="Cheque no, MoMo ref, bank transaction ID..."
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Narration <span class="text-red-500">*</span>
                            </label>

                            <textarea wire:model="narration" rows="3"
                                placeholder="State clearly what this receipt is for..."
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            03. Receipt Amount
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Amount Received <span class="text-red-500">*</span>
                            </label>

                            <input type="number" step="0.01" wire:model.live="amount_received"
                                placeholder="Enter amount received"
                                class="flex-1 text-xs bg-white border border-blue-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Amount In Words
                            </label>

                            <textarea rows="2" readonly
                                class="flex-1 text-xs bg-blue-50 border border-blue-300 px-2.5 py-2 shadow-inner text-blue-900 font-medium resize-none">{{ $amount_in_words }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            04. Approval Lines
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Prepared By</label>
                            <input type="text" wire:model="prepared_by"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Checked By</label>
                            <input type="text" wire:model="checked_by"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Approved By</label>
                            <input type="text" wire:model="approved_by"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Received By</label>
                            <input type="text" wire:model="received_by"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                    </div>
                </div>

            </div>

            <div class="xl:col-span-1">
                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden sticky top-6">
                    <div class="bg-slate-800 px-3 py-2 border-b border-slate-900">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-white">
                            Receipt Snapshot
                        </h2>
                    </div>

                    <div class="p-5 space-y-4">

                        @if($receipt_type === 'project_receipt')
                            <div class="bg-blue-50 border border-blue-200 p-4">
                                <p class="text-[10px] font-bold uppercase text-blue-700">Project Contract Value</p>
                                <p class="mt-1 text-xl font-black font-mono text-blue-900">
                                    {{ number_format((float) $project_value, 2) }}
                                </p>
                            </div>

                            <div class="bg-amber-50 border border-amber-200 p-4">
                                <p class="text-[10px] font-bold uppercase text-amber-700">Previous Receipts</p>
                                <p class="mt-1 text-xl font-black font-mono text-amber-900">
                                    {{ number_format((float) $previous_receipts, 2) }}
                                </p>
                            </div>

                            <div class="bg-purple-50 border border-purple-200 p-4">
                                <p class="text-[10px] font-bold uppercase text-purple-700">Outstanding Before Receipt</p>
                                <p class="mt-1 text-xl font-black font-mono text-purple-900">
                                    {{ number_format((float) $outstanding_before_receipt, 2) }}
                                </p>
                            </div>

                            <div class="bg-slate-50 border border-slate-200 p-4">
                                <p class="text-[10px] font-bold uppercase text-slate-600">Balance After Receipt</p>
                                <p class="mt-1 text-xl font-black font-mono text-slate-900">
                                    {{ number_format((float) $balance_after_receipt, 2) }}
                                </p>
                            </div>
                        @else
                            <div class="bg-amber-50 border border-amber-200 p-4">
                                <p class="text-[10px] font-bold uppercase text-amber-700">Previous Receipts</p>
                                <p class="mt-1 text-xl font-black font-mono text-amber-900">
                                    {{ number_format((float) $previous_receipts, 2) }}
                                </p>
                            </div>
                        @endif

                        <div class="bg-green-50 border border-green-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-green-700">Amount Received</p>
                            <p class="mt-1 text-xl font-black font-mono text-green-900">
                                {{ number_format((float) $amount_received, 2) }}
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
                Receipt Voucher Ledger
            </h2>
        </div>

        <div class="w-full bg-slate-200 border-b border-slate-300 flex items-center shadow-inner">
            <span class="pl-4 text-slate-500 font-mono text-sm select-none">🔍</span>
            <input wire:model.live.debounce.500ms="search" type="text"
                placeholder="Filter receipt vouchers..."
                class="w-full bg-transparent border-0 px-3 py-3 text-xs text-slate-900 placeholder-slate-500 focus:ring-0 outline-none font-medium">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap table-fixed">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300">
                    <tr>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Receipt No.</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Date</th>
                        <th class="w-56 px-4 py-4 border-r border-slate-200">Payer</th>
                        <th class="w-48 px-4 py-4 border-r border-slate-200">Type / Category</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Amount</th>
                        <th class="w-28 px-4 py-4 border-r border-slate-200">Status</th>
                        <th class="w-64 px-4 py-4">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($receipts as $receipt)
                        <tr class="hover:bg-blue-50/70 border-b border-slate-200 transition">
                            <td class="px-4 py-6 font-mono font-bold text-blue-800 border-r border-slate-200 bg-slate-50/50">
                                {{ $receipt->receipt_number }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono border-r border-slate-200">
                                {{ $receipt->receipt_date }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">{{ $receipt->payer_name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5">{{ $receipt->receipt_method }}</div>
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">
                                    {{ $receiptTypes[$receipt->receipt_type] ?? $receipt->receipt_type }}
                                </div>
                                <div class="text-[10px] text-slate-400 truncate mt-0.5">
                                    @if($receipt->project)
                                        {{ $receipt->project->project_name }}
                                    @elseif($receipt->category)
                                        {{ $receipt->category->name }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-6 text-green-700 font-mono border-r border-slate-200">
                                {{ number_format((float) $receipt->amount_received, 2) }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <span class="px-2 py-1 text-[10px] font-bold uppercase border
                                    @if($receipt->status === 'approved' || $receipt->status === 'received' || $receipt->status === 'posted')
                                        bg-green-50 text-green-700 border-green-300
                                    @elseif($receipt->status === 'cancelled')
                                        bg-red-50 text-red-700 border-red-300
                                    @elseif($receipt->status === 'draft')
                                        bg-amber-50 text-amber-700 border-amber-300
                                    @else
                                        bg-slate-50 text-slate-700 border-slate-300
                                    @endif">
                                    {{ strtoupper($receipt->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-6">
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="editReceipt({{ $receipt->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">Edit</button>

                                    <button type="button" wire:click="approveReceipt({{ $receipt->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-green-50 text-green-700 border border-green-300">Approve</button>

                                    <button type="button" wire:click="markReceived({{ $receipt->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-300">Received</button>

                                    <a href="{{ route('finance.receipt-vouchers.print', $receipt) }}" target="_blank"
                                        class="px-2 py-1 text-[10px] font-bold bg-slate-50 text-slate-700 border border-slate-300">Print</a>

                                    <button type="button" wire:click="cancelReceipt({{ $receipt->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">Cancel</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                [Err] 0 receipt vouchers returned.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>