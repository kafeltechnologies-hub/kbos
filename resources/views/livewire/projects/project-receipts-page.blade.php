<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

    <form id="project-receipt-form" wire:submit.prevent="postLedger">

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-green-900 px-4 py-3 flex items-center justify-between border-b border-slate-950">
                <div>
                    <span class="text-[10px] font-bold text-green-200 tracking-wider uppercase font-mono block">
                        Finance Integration Area
                    </span>

                    <h1 class="text-sm font-bold text-white">
                        Receipt Voucher Ledger — Project Receipts
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
                    class="px-4 py-1.5 text-xs font-semibold text-white bg-green-700 border border-green-800 hover:bg-green-800 shadow-sm">
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

                <div class="ml-auto px-3 py-1 bg-green-50 border border-green-200 text-[11px] font-mono font-bold text-slate-700">
                    RECEIPT NO:
                    <span class="text-green-700">
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

                        <div class="flex items-center w-full xl:col-span-2">
                            <label for="project_id" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Project <span class="text-red-500">*</span>
                            </label>

                            <select id="project_id" name="project_id" wire:model.live="project_id"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-green-600 focus:ring-0 outline-none">
                                <option value="">Select project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">
                                        {{ $project->project_code }} — {{ $project->project_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label for="date_received" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Date Received <span class="text-red-500">*</span>
                            </label>

                            <input id="date_received" name="date_received" wire:model="date_received" type="date"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="status" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Status
                            </label>

                            <select id="status" name="status" wire:model="status"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-green-600 focus:ring-0 outline-none">
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption }}">
                                        {{ strtoupper($statusOption) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

                    <div class="bg-blue-50 border border-blue-200 p-4">
                        <div class="text-[10px] uppercase font-bold text-blue-700">
                            Contract Value
                        </div>

                        <div class="mt-2 text-lg font-black font-mono text-blue-900">
                            GHS {{ number_format((float) $contract_value, 2) }}
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 p-4">
                        <div class="text-[10px] uppercase font-bold text-green-700">
                            Total Received Before
                        </div>

                        <div class="mt-2 text-lg font-black font-mono text-green-900">
                            GHS {{ number_format((float) $total_received_before, 2) }}
                        </div>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 p-4">
                        <div class="text-[10px] uppercase font-bold text-slate-600">
                            Current Receipt
                        </div>

                        <div class="mt-2 text-lg font-black font-mono text-slate-900">
                            GHS {{ number_format((float) $amount_received, 2) }}
                        </div>
                    </div>

                    <div class="bg-purple-50 border border-purple-200 p-4">
                        <div class="text-[10px] uppercase font-bold text-purple-700">
                            Outstanding Balance
                        </div>

                        <div class="mt-2 text-lg font-black font-mono text-purple-900">
                            GHS {{ number_format((float) $outstanding_balance, 2) }}
                        </div>
                    </div>

                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            02. Payer Information
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label for="received_from" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Received From
                            </label>

                            <input id="received_from" name="received_from" wire:model="received_from" type="text"
                                placeholder="Client / payer name"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="payer_phone" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Payer Phone
                            </label>

                            <input id="payer_phone" name="payer_phone" wire:model="payer_phone" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="payer_tin" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Payer TIN
                            </label>

                            <input id="payer_tin" name="payer_tin" wire:model="payer_tin" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-green-600 focus:ring-0 outline-none">
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
                            <label for="contract_value" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Contract Value
                            </label>

                            <input id="contract_value" name="contract_value"
                                value="{{ number_format((float) $contract_value, 2) }}"
                                readonly
                                class="flex-1 text-xs bg-slate-200 border border-slate-300 px-2.5 py-1.5 shadow-inner text-slate-700 cursor-not-allowed">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="amount_received" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Amount Received <span class="text-red-500">*</span>
                            </label>

                            <input id="amount_received" name="amount_received" wire:model.live="amount_received" type="number" step="0.01"
                                class="flex-1 text-xs bg-white border border-green-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            04. Receipt Method & Reference
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label for="receipt_method" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Receipt Method
                            </label>

                            <select id="receipt_method" name="receipt_method" wire:model.live="receipt_method"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                                <option value="">Select method</option>
                                @foreach($receiptMethods as $method)
                                    <option value="{{ $method }}">{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($receipt_method === 'Bank Transfer')
                            <div class="flex items-center w-full">
                                <label for="bank_name" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                    Bank Name
                                </label>

                                <input id="bank_name" name="bank_name" wire:model="bank_name" type="text"
                                    class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                            </div>

                            <div class="flex items-center w-full">
                                <label for="bank_account" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                    Bank Account
                                </label>

                                <input id="bank_account" name="bank_account" wire:model="bank_account" type="text"
                                    class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                            </div>
                        @endif

                        @if($receipt_method === 'Cheque')
                            <div class="flex items-center w-full">
                                <label for="cheque_number" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                    Cheque Number
                                </label>

                                <input id="cheque_number" name="cheque_number" wire:model="cheque_number" type="text"
                                    class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                            </div>
                        @endif

                        @if($receipt_method === 'Mobile Money')
                            <div class="flex items-center w-full">
                                <label for="momo_number" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                    MoMo Number
                                </label>

                                <input id="momo_number" name="momo_number" wire:model="momo_number" type="text"
                                    class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                            </div>
                        @endif

                        <div class="flex items-center w-full xl:col-span-2">
                            <label for="transaction_reference" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Transaction Reference
                            </label>

                            <input id="transaction_reference" name="transaction_reference" wire:model="transaction_reference" type="text"
                                placeholder="Bank reference, cheque number, MoMo transaction ID..."
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full xl:col-span-2">
                            <label for="receipt_narration" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Receipt Narration
                            </label>

                            <input id="receipt_narration" name="receipt_narration" wire:model="receipt_narration" type="text"
                                placeholder="Example: Part payment received from client for feeder extension works"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label for="remarks" class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Remarks
                            </label>

                            <textarea id="remarks" name="remarks" wire:model="remarks" rows="2"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-green-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            05. Approval Lines
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label for="prepared_by" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Prepared By
                            </label>

                            <input id="prepared_by" name="prepared_by" wire:model="prepared_by" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-green-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="approved_by" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Approved By
                            </label>

                            <input id="approved_by" name="approved_by" wire:model="approved_by" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-green-600 focus:ring-0 outline-none">
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
                        <div class="bg-blue-50 border border-blue-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-blue-700">Contract Value</p>
                            <p class="mt-1 text-xl font-black font-mono text-blue-900">
                                {{ number_format((float) $contract_value, 2) }}
                            </p>
                        </div>

                        <div class="bg-green-50 border border-green-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-green-700">Previous Receipts</p>
                            <p class="mt-1 text-xl font-black font-mono text-green-900">
                                {{ number_format((float) $total_received_before, 2) }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-slate-500">Current Receipt</p>
                            <p class="mt-1 text-xl font-black font-mono text-slate-900">
                                {{ number_format((float) $amount_received, 2) }}
                            </p>
                        </div>

                        <div class="bg-purple-50 border border-purple-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-purple-700">Outstanding Balance</p>
                            <p class="mt-1 text-xl font-black font-mono text-purple-900">
                                {{ number_format((float) $outstanding_balance, 2) }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-slate-500">Receipt Method</p>
                            <p class="mt-1 text-xs text-slate-700 leading-5">
                                {{ $receipt_method ?: 'No receipt method selected.' }}
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
                Receipt Voucher Ledger Outputs
            </h2>
        </div>

        <div class="w-full bg-slate-200 border-b border-slate-300 flex items-center shadow-inner">
            <span class="pl-4 text-slate-500 font-mono text-sm select-none">🔍</span>

            <input id="search" name="search" wire:model.live.debounce.500ms="search" type="text"
                placeholder="Filter receipt records inline..."
                class="w-full bg-transparent border-0 px-3 py-3 text-xs text-slate-900 placeholder-slate-500 focus:ring-0 outline-none font-medium">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap table-fixed">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300 select-none">
                    <tr>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Receipt No.</th>
                        <th class="w-56 px-4 py-4 border-r border-slate-200">Received From</th>
                        <th class="w-64 px-4 py-4 border-r border-slate-200">Project</th>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Contract</th>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Received</th>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Outstanding</th>
                        <th class="w-28 px-4 py-4 border-r border-slate-200">Status</th>
                        <th class="w-48 px-4 py-4">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($receipts as $receipt)
                        <tr class="hover:bg-green-50/70 border-b border-slate-200 transition">
                            <td class="px-4 py-6 font-mono font-bold text-green-800 border-r border-slate-200 bg-slate-50/50">
                                {{ $receipt->receipt_number ?? '-' }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">
                                    {{ $receipt->received_from ?? '-' }}
                                </div>

                                <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5">
                                    {{ $receipt->payer_phone ?: 'PAYER' }}
                                </div>
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">
                                    {{ $receipt->project?->project_name ?: 'NO PROJECT' }}
                                </div>

                                <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5">
                                    {{ $receipt->project?->project_code ?: 'UNKNOWN_PROJECT' }}
                                </div>
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono border-r border-slate-200">
                                {{ number_format((float) $receipt->contract_value, 2) }}
                            </td>

                            <td class="px-4 py-6 text-green-700 font-mono border-r border-slate-200">
                                {{ number_format((float) $receipt->amount_received, 2) }}
                            </td>

                            <td class="px-4 py-6 text-purple-700 font-mono border-r border-slate-200">
                                {{ number_format((float) $receipt->outstanding_balance, 2) }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <span class="px-2 py-1 text-[10px] font-bold uppercase border
                                    @if($receipt->status === 'approved')
                                        bg-green-50 text-green-700 border-green-300
                                    @elseif($receipt->status === 'reversed')
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
                                    <button type="button"
                                        wire:click="editReceipt({{ $receipt->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">
                                        Edit
                                    </button>

                                    <button type="button"
                                        wire:click="approveReceipt({{ $receipt->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-green-50 text-green-700 border border-green-300">
                                        Approve
                                    </button>

                                    <a href="{{ route('projects.receipts.print', $receipt) }}"
                                        target="_blank"
                                        class="px-2 py-1 text-[10px] font-bold bg-slate-50 text-slate-700 border border-slate-300">
                                        Print
                                    </a>

                                    <button type="button"
                                        wire:click="reverseReceipt({{ $receipt->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                        Reverse
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                [Err] 0 receipt records returned based on query arguments.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>