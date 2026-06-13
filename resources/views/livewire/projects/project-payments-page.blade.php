<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

    <form id="project-payment-form" wire:submit.prevent="postLedger">

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-blue-900 px-4 py-3 flex items-center justify-between border-b border-slate-950">
                <div>
                    <span class="text-[10px] font-bold text-blue-200 tracking-wider uppercase font-mono block">
                        Finance Integration Area
                    </span>

                    <h1 class="text-sm font-bold text-white">
                        Voucher Ledger — Payment, Receipt & Journal Voucher
                    </h1>
                </div>

                <div class="hidden sm:block border-l border-slate-600 pl-4 text-right">
                    <span class="text-[10px] block uppercase font-mono text-slate-300">
                        Voucher Records
                    </span>

                    <span class="text-base font-black font-mono text-white">
                        {{ $payments->count() }}
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
                    Post Voucher
                </button>

                <button type="button"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Print Voucher
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
                    VOUCHER NO:
                    <span class="text-blue-700">
                        {{ $this->generateVoucherNumber() }}
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
                            <label for="voucher_type" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Voucher Type
                            </label>

                            <select id="voucher_type" name="voucher_type" wire:model="voucher_type"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                                @foreach($voucherTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label for="payment_date" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Voucher Date
                            </label>

                            <input id="payment_date" name="payment_date" wire:model="payment_date" type="date"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full xl:col-span-2">
                            <label for="project_id" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Project
                            </label>

                            <select id="project_id" name="project_id" wire:model.live="project_id"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                                <option value="">Select project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">
                                        {{ $project->project_code }} — {{ $project->project_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

                    <div class="bg-blue-50 border border-blue-200 p-4">
                        <div class="text-[10px] uppercase font-bold text-blue-700">
                            Project Contract Value
                        </div>

                        <div class="mt-2 text-lg font-black font-mono text-blue-900">
                            GHS {{ number_format((float) $project_value, 2) }}
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 p-4">
                        <div class="text-[10px] uppercase font-bold text-amber-700">
                            Total Payments Made
                        </div>

                        <div class="mt-2 text-lg font-black font-mono text-amber-900">
                            GHS {{ number_format((float) $project_cost, 2) }}
                        </div>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 p-4">
                        <div class="text-[10px] uppercase font-bold text-slate-600">
                            Gross Amount
                        </div>

                        <div class="mt-2 text-lg font-black font-mono text-slate-900">
                            GHS {{ number_format((float) $gross_amount, 2) }}
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
                            02. Payee / Payer Details
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label for="payee_name" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Payee / Payer Name
                            </label>

                            <input id="payee_name" name="payee_name" wire:model="payee_name" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="payee_type" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Payee Type
                            </label>

                            <select id="payee_type" name="payee_type" wire:model="payee_type"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                                <option value="">Select type</option>
                                @foreach($payeeTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label for="payee_account" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Account / Wallet
                            </label>

                            <input id="payee_account" name="payee_account" wire:model="payee_account" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="payee_phone" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Phone
                            </label>

                            <input id="payee_phone" name="payee_phone" wire:model="payee_phone" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
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
                            <label for="gross_amount" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Gross Amount
                            </label>

                            <input id="gross_amount" name="gross_amount"
                                value="{{ number_format((float) $gross_amount, 2) }}"
                                readonly
                                class="flex-1 text-xs bg-slate-200 border border-slate-300 px-2.5 py-1.5 shadow-inner text-slate-700 cursor-not-allowed">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="amount_paid" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Amount Being Paid
                            </label>

                            <input id="amount_paid" name="amount_paid" wire:model.live="amount_paid" type="number" step="0.01"
                                class="flex-1 text-xs bg-white border border-blue-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="vat_applicable" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                VAT Applicable?
                            </label>

                            <select id="vat_applicable" name="vat_applicable" wire:model.live="vat_applicable"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label for="vat_amount" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                VAT 15%
                            </label>

                            <input id="vat_amount" name="vat_amount" value="{{ number_format((float) $vat_amount, 2) }}" readonly
                                class="flex-1 text-xs bg-slate-200 border border-slate-300 px-2.5 py-1.5 shadow-inner text-slate-700">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="getfund_amount" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                GETFund 2.5%
                            </label>

                            <input id="getfund_amount" name="getfund_amount" value="{{ number_format((float) $getfund_amount, 2) }}" readonly
                                class="flex-1 text-xs bg-slate-200 border border-slate-300 px-2.5 py-1.5 shadow-inner text-slate-700">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="nhil_amount" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                NHIL 2.5%
                            </label>

                            <input id="nhil_amount" name="nhil_amount" value="{{ number_format((float) $nhil_amount, 2) }}" readonly
                                class="flex-1 text-xs bg-slate-200 border border-slate-300 px-2.5 py-1.5 shadow-inner text-slate-700">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="net_amount" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Net Amount
                            </label>

                            <input id="net_amount" name="net_amount" value="{{ number_format((float) $net_amount, 2) }}" readonly
                                class="flex-1 text-xs bg-green-50 border border-green-300 px-2.5 py-1.5 shadow-inner text-green-800 font-bold">
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            04. Payment Method & Reference
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label for="payment_method" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Payment Method
                            </label>

                            <select id="payment_method" name="payment_method" wire:model.live="payment_method"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                                <option value="">Select method</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method }}">{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($payment_method === 'Bank Transfer')
                            <div class="flex items-center w-full">
                                <label for="bank_name" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                    Bank Name
                                </label>

                                <input id="bank_name" name="bank_name" wire:model="bank_name" type="text"
                                    class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                            </div>

                            <div class="flex items-center w-full">
                                <label for="bank_account" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                    Bank Account
                                </label>

                                <input id="bank_account" name="bank_account" wire:model="bank_account" type="text"
                                    class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                            </div>
                        @endif

                        @if($payment_method === 'Cheque')
                            <div class="flex items-center w-full">
                                <label for="cheque_number" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                    Cheque Number
                                </label>

                                <input id="cheque_number" name="cheque_number" wire:model="cheque_number" type="text"
                                    class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                            </div>
                        @endif

                        @if($payment_method === 'Mobile Money')
                            <div class="flex items-center w-full">
                                <label for="momo_number" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                    MoMo Number
                                </label>

                                <input id="momo_number" name="momo_number" wire:model="momo_number" type="text"
                                    class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                            </div>
                        @endif

                        <div class="flex items-center w-full xl:col-span-2">
                            <label for="transaction_reference" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Transaction Reference
                            </label>

                            <input id="transaction_reference" name="transaction_reference" wire:model="transaction_reference" type="text"
                                placeholder="Bank reference, cheque no, MoMo reference..."
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full xl:col-span-2">
                            <label for="payment_narration" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Payment Narration
                            </label>

                            <input id="payment_narration" name="payment_narration" wire:model="payment_narration" type="text"
                                placeholder="Example: Part payment for cable supply and pole installation"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label for="remarks" class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Remarks
                            </label>

                            <textarea id="remarks" name="remarks" wire:model="remarks" rows="2"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>

                    </div>
                </div>

            </div>

            <div class="xl:col-span-1">
                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden sticky top-6">
                    <div class="bg-slate-800 px-3 py-2 border-b border-slate-900">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-white">
                            Voucher Snapshot
                        </h2>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="bg-blue-50 border border-blue-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-blue-700">Contract Value</p>
                            <p class="mt-1 text-xl font-black font-mono text-blue-900">
                                {{ number_format((float) $project_value, 2) }}
                            </p>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-amber-700">Total Payments Made</p>
                            <p class="mt-1 text-xl font-black font-mono text-amber-900">
                                {{ number_format((float) $project_cost, 2) }}
                            </p>
                        </div>

                        <div class="bg-purple-50 border border-purple-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-purple-700">Outstanding Balance</p>
                            <p class="mt-1 text-xl font-black font-mono text-purple-900">
                                {{ number_format((float) $outstanding_balance, 2) }}
                            </p>
                        </div>

                        <div class="bg-green-50 border border-green-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-green-700">Current Net Amount</p>
                            <p class="mt-1 text-xl font-black font-mono text-green-900">
                                {{ number_format((float) $net_amount, 2) }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-slate-500">Payment Method</p>
                            <p class="mt-1 text-xs text-slate-700 leading-5">
                                {{ $payment_method ?: 'No payment method selected.' }}
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
                Voucher Ledger Outputs
            </h2>
        </div>

        <div class="w-full bg-slate-200 border-b border-slate-300 flex items-center shadow-inner">
            <span class="pl-4 text-slate-500 font-mono text-sm select-none">🔍</span>

            <input id="search" name="search" wire:model.live.debounce.500ms="search" type="text"
                placeholder="Filter voucher records inline..."
                class="w-full bg-transparent border-0 px-3 py-3 text-xs text-slate-900 placeholder-slate-500 focus:ring-0 outline-none font-medium">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap table-fixed">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300 select-none">
                    <tr>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Voucher No.</th>
                        <th class="w-56 px-4 py-4 border-r border-slate-200">Payee / Payer</th>
                        <th class="w-64 px-4 py-4 border-r border-slate-200">Project</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Paid</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">VAT</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">GETFund</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">NHIL</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Net</th>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Method</th>
                        <th class="w-32 px-4 py-4">Date</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-blue-50/70 border-b border-slate-200 transition">
                            <td class="px-4 py-6 font-mono font-bold text-blue-800 border-r border-slate-200 bg-slate-50/50">
                                {{ $payment->voucher_number ?? '-' }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">
                                    {{ $payment->payee_name ?? '-' }}
                                </div>

                                <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5">
                                    {{ $payment->payee_type ?: 'PAYEE/PAYER' }}
                                </div>
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">
                                    {{ $payment->project?->project_name ?: 'NO PROJECT' }}
                                </div>

                                <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5">
                                    {{ $payment->project?->project_code ?: 'UNKNOWN_PROJECT' }}
                                </div>
                            </td>

                            <td class="px-4 py-6 text-blue-700 font-mono border-r border-slate-200">
                                {{ number_format((float) ($payment->amount_paid ?? 0), 2) }}
                            </td>

                            <td class="px-4 py-6 text-amber-700 font-mono border-r border-slate-200">
                                {{ number_format((float) ($payment->vat_amount ?? 0), 2) }}
                            </td>

                            <td class="px-4 py-6 text-amber-700 font-mono border-r border-slate-200">
                                {{ number_format((float) ($payment->getfund_amount ?? 0), 2) }}
                            </td>

                            <td class="px-4 py-6 text-amber-700 font-mono border-r border-slate-200">
                                {{ number_format((float) ($payment->nhil_amount ?? 0), 2) }}
                            </td>

                            <td class="px-4 py-6 text-green-700 font-mono border-r border-slate-200">
                                {{ number_format((float) $payment->net_amount, 2) }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 border-r border-slate-200">
                                {{ $payment->payment_method ?: '-' }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono">
                                {{ $payment->payment_date }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                [Err] 0 voucher records returned based on query arguments.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>