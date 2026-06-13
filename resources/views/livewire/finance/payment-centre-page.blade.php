<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

    <form id="payment-centre-form" wire:submit.prevent="postLedger">

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-900 px-4 py-3 flex items-center justify-between border-b border-slate-950">
                <div>
                    <span class="text-[10px] font-bold text-emerald-200 tracking-wider uppercase font-mono block">
                        Finance Control Area
                    </span>

                    <h1 class="text-sm font-bold text-white">
                        Payment Centre — Project Payments, Expenses, Payroll, Assets & Statutory Payments
                    </h1>
                </div>

                <div class="hidden sm:block border-l border-slate-600 pl-4 text-right">
                    <span class="text-[10px] block uppercase font-mono text-slate-300">
                        Voucher Records
                    </span>

                    <span class="text-base font-black font-mono text-white">
                        {{ $vouchers->count() }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 bg-slate-50 px-3 py-2 border-b border-slate-200">

                <button type="button" wire:click="createNew()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Create New
                </button>

                <button type="submit"
                    class="px-4 py-1.5 text-xs font-semibold text-white bg-emerald-700 border border-emerald-800 hover:bg-emerald-800 shadow-sm">
                    {{ $isEditing ? 'Update Voucher' : 'Post Voucher' }}
                </button>

                <button type="button" wire:click="clearBuffer()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Clear Buffer
                </button>

                <button type="button" wire:click="sync()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Sync
                </button>

                <div class="ml-auto px-3 py-1 bg-emerald-50 border border-emerald-200 text-[11px] font-mono font-bold text-slate-700">
                    VOUCHER NO:
                    <span class="text-emerald-700">
                        {{ $isEditing ? 'EDIT MODE' : $this->generateVoucherNumber() }}
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
                            01. Voucher Header
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Voucher Date
                            </label>

                            <input type="date" wire:model="voucher_date"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-emerald-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Status
                            </label>

                            <select wire:model="status"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-emerald-600 focus:ring-0 outline-none">
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption }}">{{ strtoupper($statusOption) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Payment Type
                            </label>

                            <select wire:model.live="payment_type"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-emerald-600 focus:ring-0 outline-none">
                                @foreach($paymentTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            02. Payment Classification
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        @if($payment_type === 'project_payment')
                            <div class="flex items-center w-full xl:col-span-2">
                                <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                    Project <span class="text-red-500">*</span>
                                </label>

                                <select wire:model.live="project_id"
                                    class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-emerald-600 focus:ring-0 outline-none">
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
                                    Expense Category <span class="text-red-500">*</span>
                                </label>

                                <select wire:model.live="expense_category_id"
                                    class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-emerald-600 focus:ring-0 outline-none">
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
                                Payee <span class="text-red-500">*</span>
                            </label>

                            <input type="text" wire:model="payee_name"
                                placeholder="Supplier, staff, subcontractor, statutory agency..."
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-emerald-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Payment Method <span class="text-red-500">*</span>
                            </label>

                            <select wire:model="payment_method"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-emerald-600 focus:ring-0 outline-none">
                                <option value="">Select method</option>
                                @foreach($paymentMethods as $method)
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
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-emerald-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Narration <span class="text-red-500">*</span>
                            </label>

                            <textarea wire:model="narration" rows="3"
                                placeholder="State clearly why this payment is being made..."
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-emerald-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            03. Payment Computation
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Amount To Pay <span class="text-red-500">*</span>
                            </label>

                            <input type="number" step="0.01" wire:model.live="gross_amount"
                                placeholder="Enter amount to pay"
                                class="flex-1 text-xs bg-white border border-emerald-300 px-2.5 py-1.5 shadow-inner focus:border-emerald-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                VAT Applicable?
                            </label>

                            <select wire:model.live="vat_applicable"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-emerald-600 focus:ring-0 outline-none">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                VAT 15%
                            </label>

                            <input value="{{ number_format((float) $vat_amount, 2) }}" readonly
                                class="flex-1 text-xs bg-slate-200 border border-slate-300 px-2.5 py-1.5 shadow-inner text-slate-700 cursor-not-allowed">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                GETFund 2.5%
                            </label>

                            <input value="{{ number_format((float) $getfund_amount, 2) }}" readonly
                                class="flex-1 text-xs bg-slate-200 border border-slate-300 px-2.5 py-1.5 shadow-inner text-slate-700 cursor-not-allowed">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                NHIL 2.5%
                            </label>

                            <input value="{{ number_format((float) $nhil_amount, 2) }}" readonly
                                class="flex-1 text-xs bg-slate-200 border border-slate-300 px-2.5 py-1.5 shadow-inner text-slate-700 cursor-not-allowed">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Withholding Tax
                            </label>

                            <input type="number" step="0.01" wire:model.live="withholding_tax"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-emerald-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Net Payment
                            </label>

                            <input value="{{ number_format((float) $net_payment, 2) }}" readonly
                                class="flex-1 text-sm font-black font-mono bg-green-50 border border-green-300 px-2.5 py-2 shadow-inner text-green-900 cursor-not-allowed">
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
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Prepared By
                            </label>

                            <input type="text" wire:model="prepared_by"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-emerald-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Approved By
                            </label>

                            <input type="text" wire:model="approved_by"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-emerald-600 focus:ring-0 outline-none">
                        </div>

                    </div>
                </div>

            </div>

            <div class="xl:col-span-1">
                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden sticky top-6">
                    <div class="bg-slate-800 px-3 py-2 border-b border-slate-900">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-white">
                            Payment Snapshot
                        </h2>
                    </div>

                    <div class="p-5 space-y-4">

                        @if($payment_type === 'project_payment')
                            <div class="bg-blue-50 border border-blue-200 p-4">
                                <p class="text-[10px] font-bold uppercase text-blue-700">Project Contract Value</p>
                                <p class="mt-1 text-xl font-black font-mono text-blue-900">
                                    {{ number_format((float) $project_value, 2) }}
                                </p>
                            </div>

                            <div class="bg-amber-50 border border-amber-200 p-4">
                                <p class="text-[10px] font-bold uppercase text-amber-700">Previous Project Payments</p>
                                <p class="mt-1 text-xl font-black font-mono text-amber-900">
                                    {{ number_format((float) $previous_payments, 2) }}
                                </p>
                            </div>

                            <div class="bg-purple-50 border border-purple-200 p-4">
                                <p class="text-[10px] font-bold uppercase text-purple-700">Outstanding Before This Payment</p>
                                <p class="mt-1 text-xl font-black font-mono text-purple-900">
                                    {{ number_format((float) $outstanding_balance, 2) }}
                                </p>
                            </div>
                        @else
                            <div class="bg-amber-50 border border-amber-200 p-4">
                                <p class="text-[10px] font-bold uppercase text-amber-700">Previous Payments</p>
                                <p class="mt-1 text-xl font-black font-mono text-amber-900">
                                    {{ number_format((float) $previous_payments, 2) }}
                                </p>
                            </div>
                        @endif

                        <div class="bg-slate-50 border border-slate-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-slate-600">Amount To Pay</p>
                            <p class="mt-1 text-xl font-black font-mono text-slate-900">
                                {{ number_format((float) $gross_amount, 2) }}
                            </p>
                        </div>

                        <div class="bg-red-50 border border-red-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-red-700">Withholding Tax</p>
                            <p class="mt-1 text-xl font-black font-mono text-red-900">
                                {{ number_format((float) $withholding_tax, 2) }}
                            </p>
                        </div>

                        <div class="bg-green-50 border border-green-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-green-700">Net Payment</p>
                            <p class="mt-1 text-xl font-black font-mono text-green-900">
                                {{ number_format((float) $net_payment, 2) }}
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
                Payment Voucher Ledger
            </h2>
        </div>

        <div class="w-full bg-slate-200 border-b border-slate-300 flex items-center shadow-inner">
            <span class="pl-4 text-slate-500 font-mono text-sm select-none">🔍</span>

            <input wire:model.live.debounce.500ms="search" type="text"
                placeholder="Filter payment vouchers..."
                class="w-full bg-transparent border-0 px-3 py-3 text-xs text-slate-900 placeholder-slate-500 focus:ring-0 outline-none font-medium">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap table-fixed">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300 select-none">
                    <tr>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Voucher No.</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Date</th>
                        <th class="w-56 px-4 py-4 border-r border-slate-200">Payee</th>
                        <th class="w-48 px-4 py-4 border-r border-slate-200">Type / Category</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Amount Paid</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Net</th>
                        <th class="w-28 px-4 py-4 border-r border-slate-200">Status</th>
                        <th class="w-64 px-4 py-4">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($vouchers as $voucher)
                        <tr class="hover:bg-emerald-50/70 border-b border-slate-200 transition">
                            <td class="px-4 py-6 font-mono font-bold text-emerald-800 border-r border-slate-200 bg-slate-50/50">
                                {{ $voucher->voucher_number }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono border-r border-slate-200">
                                {{ $voucher->voucher_date }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">
                                    {{ $voucher->payee_name }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5">
                                    {{ $voucher->payment_method }}
                                </div>
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">
                                    {{ $paymentTypes[$voucher->payment_type] ?? $voucher->payment_type }}
                                </div>

                                <div class="text-[10px] text-slate-400 truncate mt-0.5">
                                    @if($voucher->project)
                                        {{ $voucher->project->project_name }}
                                    @elseif($voucher->category)
                                        {{ $voucher->category->name }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-6 text-slate-700 font-mono border-r border-slate-200">
                                {{ number_format((float) $voucher->gross_amount, 2) }}
                            </td>

                            <td class="px-4 py-6 text-green-700 font-mono border-r border-slate-200">
                                {{ number_format((float) $voucher->net_payment, 2) }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <span class="px-2 py-1 text-[10px] font-bold uppercase border
                                    @if($voucher->status === 'approved' || $voucher->status === 'paid' || $voucher->status === 'posted')
                                        bg-green-50 text-green-700 border-green-300
                                    @elseif($voucher->status === 'cancelled')
                                        bg-red-50 text-red-700 border-red-300
                                    @elseif($voucher->status === 'draft')
                                        bg-amber-50 text-amber-700 border-amber-300
                                    @else
                                        bg-slate-50 text-slate-700 border-slate-300
                                    @endif">
                                    {{ strtoupper($voucher->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-6">
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="editVoucher({{ $voucher->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">
                                        Edit
                                    </button>

                                    <button type="button" wire:click="approveVoucher({{ $voucher->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-green-50 text-green-700 border border-green-300">
                                        Approve
                                    </button>

                                    <button type="button" wire:click="markPaid({{ $voucher->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-300">
                                        Paid
                                    </button>

                                    <a href="{{ route('finance.payment-vouchers.print', $voucher) }}" target="_blank"
                                        class="px-2 py-1 text-[10px] font-bold bg-slate-50 text-slate-700 border border-slate-300">
                                        Print
                                    </a>

                                    <button type="button" wire:click="cancelVoucher({{ $voucher->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                        Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                [Err] 0 payment vouchers returned.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>