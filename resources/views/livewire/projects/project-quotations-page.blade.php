<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

    <form id="project-quotation-form" wire:submit.prevent="postLedger">

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-blue-900 px-4 py-3 flex items-center justify-between border-b border-slate-950">
                <div>
                    <span class="text-[10px] font-bold text-blue-200 tracking-wider uppercase font-mono block">
                        Commercial Area
                    </span>

                    <h1 class="text-sm font-bold text-white">
                        Quotation Ledger — Project Pricing & Client Offers
                    </h1>
                </div>

                <div class="hidden sm:block border-l border-slate-600 pl-4 text-right">
                    <span class="text-[10px] block uppercase font-mono text-slate-300">
                        Quotation Records
                    </span>

                    <span class="text-base font-black font-mono text-white">
                        {{ $quotations->count() }}
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
                    {{ $isEditing ? 'Update Quotation' : 'Post Quotation' }}
                </button>

                <button type="button" wire:click="addItem()"
                    class="px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50 border border-green-300 hover:bg-green-100 shadow-sm">
                    Add Item
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
                    QUOTATION NO:
                    <span class="text-blue-700">
                        {{ $isEditing ? 'EDIT MODE' : $this->generateQuotationNumber() }}
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
                            01. Quotation Header
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label for="company_id" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Company
                            </label>

                            <select id="company_id" name="company_id" wire:model="company_id"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                                <option value="">Select company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label for="client_id" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Client
                            </label>

                            <select id="client_id" name="client_id" wire:model.live="client_id"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                                <option value="">Select client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label for="quotation_date" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Quotation Date
                            </label>

                            <input id="quotation_date" name="quotation_date" wire:model="quotation_date" type="date"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="valid_until" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Valid Until
                            </label>

                            <input id="valid_until" name="valid_until" wire:model="valid_until" type="date"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full xl:col-span-2">
                            <label for="project_id" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Existing Project
                            </label>

                            <select id="project_id" name="project_id" wire:model.live="project_id"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                                <option value="">Optional - link to existing project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">
                                        {{ $project->project_code }} — {{ $project->project_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            02. Client Information
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label for="client_name" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Client Name <span class="text-red-500">*</span>
                            </label>

                            <input id="client_name" name="client_name" wire:model="client_name" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="client_phone" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Phone
                            </label>

                            <input id="client_phone" name="client_phone" wire:model="client_phone" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="client_email" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Email
                            </label>

                            <input id="client_email" name="client_email" wire:model="client_email" type="email"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="client_tin" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                TIN
                            </label>

                            <input id="client_tin" name="client_tin" wire:model="client_tin" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label for="client_address" class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Address
                            </label>

                            <textarea id="client_address" name="client_address" wire:model="client_address" rows="2"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            03. Project / Scope Details
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 gap-y-5">

                        <div class="flex items-center w-full">
                            <label for="project_title" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Project Title <span class="text-red-500">*</span>
                            </label>

                            <input id="project_title" name="project_title" wire:model="project_title" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-start w-full">
                            <label for="scope_of_work" class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Scope of Work
                            </label>

                            <textarea id="scope_of_work" name="scope_of_work" wire:model="scope_of_work" rows="3"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>

                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200 flex items-center justify-between">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            04. Quotation Items
                        </h2>

                        <button type="button" wire:click="addItem()"
                            class="px-2 py-1 text-[10px] font-bold bg-green-50 text-green-700 border border-green-300">
                            Add Line
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-slate-700 border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-3 w-28">Code</th>
                                    <th class="px-3 py-3 min-w-[280px]">Description</th>
                                    <th class="px-3 py-3 w-24">Unit</th>
                                    <th class="px-3 py-3 w-28">Qty</th>
                                    <th class="px-3 py-3 w-36">Unit Price</th>
                                    <th class="px-3 py-3 w-36">Line Total</th>
                                    <th class="px-3 py-3 w-20">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200">
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td class="px-3 py-3">
                                            <input wire:model.live="items.{{ $index }}.item_code" type="text"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3">
                                            <textarea wire:model.live="items.{{ $index }}.description" rows="2"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5 resize-none"></textarea>
                                        </td>

                                        <td class="px-3 py-3">
                                            <input wire:model.live="items.{{ $index }}.unit" type="text"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3">
                                            <input wire:model.live="items.{{ $index }}.quantity" type="number" step="0.01"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3">
                                            <input wire:model.live="items.{{ $index }}.unit_price" type="number" step="0.01"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 font-mono font-bold text-slate-700">
                                            {{ number_format((float) ($item['line_total'] ?? 0), 2) }}
                                        </td>

                                        <td class="px-3 py-3">
                                            <button type="button" wire:click="removeItem({{ $index }})"
                                                class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            05. Terms & Approval
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label for="vat_applicable" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                VAT Applicable?
                            </label>

                            <select id="vat_applicable" name="vat_applicable" wire:model.live="vat_applicable"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label for="status" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Status
                            </label>

                            <select id="status" name="status" wire:model="status"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5">
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption }}">{{ strtoupper($statusOption) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label for="amount_in_words" class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Amount In Words
                            </label>

                            <textarea id="amount_in_words" rows="2" readonly
                                class="flex-1 text-xs bg-blue-50 border border-blue-300 px-2.5 py-2 shadow-inner text-blue-900 font-medium resize-none">{{ $amount_in_words }}</textarea>
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label for="terms_and_conditions" class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Terms & Conditions
                            </label>

                            <textarea id="terms_and_conditions" wire:model="terms_and_conditions" rows="3"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 resize-none"></textarea>
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label for="notes" class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                                Notes
                            </label>

                            <textarea id="notes" wire:model="notes" rows="2"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 resize-none"></textarea>
                        </div>

                        <div class="flex items-center w-full">
                            <label for="prepared_by" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Prepared By
                            </label>

                            <input id="prepared_by" wire:model="prepared_by" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5">
                        </div>

                        <div class="flex items-center w-full">
                            <label for="approved_by" class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Approved By
                            </label>

                            <input id="approved_by" wire:model="approved_by" type="text"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5">
                        </div>

                    </div>
                </div>

            </div>

            <div class="xl:col-span-1">
                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden sticky top-6">
                    <div class="bg-slate-800 px-3 py-2 border-b border-slate-900">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-white">
                            Quotation Summary
                        </h2>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="bg-slate-50 border border-slate-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-slate-600">Subtotal</p>
                            <p class="mt-1 text-xl font-black font-mono text-slate-900">
                                {{ number_format((float) $subtotal, 2) }}
                            </p>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-amber-700">VAT / Levies</p>
                            <p class="mt-1 text-xs font-mono text-amber-900">
                                VAT: {{ number_format((float) $vat_amount, 2) }}<br>
                                GETFund: {{ number_format((float) $getfund_amount, 2) }}<br>
                                NHIL: {{ number_format((float) $nhil_amount, 2) }}
                            </p>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-blue-700">Grand Total</p>
                            <p class="mt-1 text-xl font-black font-mono text-blue-900">
                                {{ number_format((float) $grand_total, 2) }}
                            </p>
                        </div>

                        <div class="bg-green-50 border border-green-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-green-700">Amount In Words</p>
                            <p class="mt-2 text-xs leading-5 font-medium text-green-900">
                                {{ $amount_in_words ?: 'Amount in words will appear here.' }}
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
                Quotation Ledger Outputs
            </h2>
        </div>

        <div class="w-full bg-slate-200 border-b border-slate-300 flex items-center shadow-inner">
            <span class="pl-4 text-slate-500 font-mono text-sm select-none">🔍</span>

            <input id="search" name="search" wire:model.live.debounce.500ms="search" type="text"
                placeholder="Filter quotation records inline..."
                class="w-full bg-transparent border-0 px-3 py-3 text-xs text-slate-900 placeholder-slate-500 focus:ring-0 outline-none font-medium">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap table-fixed">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300 select-none">
                    <tr>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Quotation No.</th>
                        <th class="w-56 px-4 py-4 border-r border-slate-200">Client</th>
                        <th class="w-64 px-4 py-4 border-r border-slate-200">Project Title</th>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Grand Total</th>
                        <th class="w-28 px-4 py-4 border-r border-slate-200">Status</th>
                        <th class="w-52 px-4 py-4">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($quotations as $quotation)
                        <tr class="hover:bg-blue-50/70 border-b border-slate-200 transition">
                            <td class="px-4 py-6 font-mono font-bold text-blue-800 border-r border-slate-200 bg-slate-50/50">
                                {{ $quotation->quotation_number ?? '-' }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">
                                    {{ $quotation->client_name ?? '-' }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5">
                                    {{ $quotation->client_phone ?: 'CLIENT' }}
                                </div>
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                {{ $quotation->project_title ?? '-' }}
                            </td>

                            <td class="px-4 py-6 text-blue-700 font-mono border-r border-slate-200">
                                {{ number_format((float) $quotation->grand_total, 2) }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <span class="px-2 py-1 text-[10px] font-bold uppercase border bg-slate-50 text-slate-700 border-slate-300">
                                    {{ strtoupper($quotation->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-6">
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="editQuotation({{ $quotation->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">
                                        Edit
                                    </button>

                                    <button type="button" wire:click="approveQuotation({{ $quotation->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-green-50 text-green-700 border border-green-300">
                                        Approve
                                    </button>

                                    <a href="{{ route('projects.quotations.print', $quotation) }}" target="_blank"
                                        class="px-2 py-1 text-[10px] font-bold bg-slate-50 text-slate-700 border border-slate-300">
                                        Print
                                    </a>

                                    <button type="button" wire:click="cancelQuotation({{ $quotation->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                        Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                [Err] 0 quotation records returned.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>