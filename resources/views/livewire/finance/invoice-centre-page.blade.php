<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

    <form id="invoice-centre-form" wire:submit.prevent="postLedger">

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 px-4 py-3 flex items-center justify-between border-b border-slate-950">
                <div>
                    <span class="text-[10px] font-bold text-indigo-200 tracking-wider uppercase font-mono block">
                        Finance Control Area
                    </span>
                    <h1 class="text-sm font-bold text-white">
                        Invoice Centre — Quotation & Invoice Preparation
                    </h1>
                </div>

                <div class="hidden sm:block border-l border-slate-600 pl-4 text-right">
                    <span class="text-[10px] block uppercase font-mono text-slate-300">Invoice Records</span>
                    <span class="text-base font-black font-mono text-white">{{ $invoices->count() }}</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 bg-slate-50 px-3 py-2 border-b border-slate-200">
                <button type="button" wire:click="createNew()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                    Create New
                </button>

                <button type="submit"
                    class="px-4 py-1.5 text-xs font-semibold text-white bg-green-700 border border-green-800 hover:bg-green-800 shadow-sm">
                    {{ $isEditing ? 'Update Invoice' : 'Post Invoice' }}
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

                <div class="ml-auto px-3 py-1 bg-indigo-50 border border-indigo-200 text-[11px] font-mono font-bold text-slate-700">
                    DOCUMENT NO:
                    <span class="text-indigo-700">
                        {{ $isEditing ? 'EDIT MODE' : $this->generateInvoiceNumber() }}
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
                            01. Document Header
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Document Type
                            </label>

                            <select wire:model.live="document_type"
                                class="flex-1 text-xs bg-white border border-indigo-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                                @foreach($documentTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Status
                            </label>

                            <select wire:model="status"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-indigo-600 focus:ring-0 outline-none">
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption }}">{{ strtoupper($statusOption) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Company</label>
                            <select wire:model="company_id"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-indigo-600 focus:ring-0 outline-none">
                                <option value="">Select company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Client</label>
                            <select wire:model.live="client_id"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-indigo-600 focus:ring-0 outline-none">
                                <option value="">Select client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                {{ $document_type === 'quotation' ? 'Quotation Date' : 'Invoice Date' }}
                            </label>
                            <input type="date" wire:model="invoice_date"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-indigo-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                {{ $document_type === 'quotation' ? 'Valid Until' : 'Due Date' }}
                            </label>
                            <input type="date" wire:model="due_date"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-indigo-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Load Quotation</label>
                            <select wire:model.live="project_quotation_id"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                                <option value="">Optional - load quotation into document</option>
                                @foreach($quotations as $quotation)
                                    <option value="{{ $quotation->id }}">
                                        {{ $quotation->quotation_number }} — {{ $quotation->client_name }} — {{ $quotation->project_title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Project</label>
                            <select wire:model.live="project_id"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                                <option value="">Optional - link to project</option>
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
                            02. Client Details
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">
                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Client Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="client_name"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Phone</label>
                            <input type="text" wire:model="client_phone"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Email</label>
                            <input type="email" wire:model="client_email"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">TIN</label>
                            <input type="text" wire:model="client_tin"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">Address</label>
                            <textarea wire:model="client_address" rows="2"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            03. Project / Scope
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 gap-y-5">
                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                                Project Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="project_title"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-start w-full">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">Scope of Work</label>
                            <textarea wire:model="scope_of_work" rows="3"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200 flex items-center justify-between">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                            04. Document Items
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
                                    <th class="px-3 py-3 min-w-[240px]">Material / Service</th>
                                    <th class="px-3 py-3 min-w-[300px]">Description</th>
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
                                        <td class="px-3 py-3 align-top">
                                            <select wire:model.live="items.{{ $index }}.material_id"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                                <option value="">Select material</option>
                                                @foreach($materials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ $material->material_code }} — {{ $material->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="mt-1 text-[10px] font-mono text-slate-400">
                                                {{ $item['item_code'] ?? '' }}
                                            </div>
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <textarea wire:model.live="items.{{ $index }}.description" rows="2"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5 resize-none"></textarea>
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input wire:model.live="items.{{ $index }}.unit" type="text"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input wire:model.live="items.{{ $index }}.quantity" type="number" step="0.01"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top">
                                            <input wire:model.live="items.{{ $index }}.unit_price" type="number" step="0.01"
                                                class="w-full text-xs bg-white border border-slate-300 px-2 py-1.5">
                                        </td>

                                        <td class="px-3 py-3 align-top font-mono font-bold text-slate-700">
                                            {{ number_format((float) ($item['line_total'] ?? 0), 2) }}
                                        </td>

                                        <td class="px-3 py-3 align-top">
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
                            05. Extra Charges, Tax & Approval
                        </h2>
                    </div>

                    <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">
                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Labor Charge</label>
                            <input type="number" step="0.01" wire:model.live="labor_charge"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Transport Charge</label>
                            <input type="number" step="0.01" wire:model.live="transport_charge"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Other Charges</label>
                            <input type="number" step="0.01" wire:model.live="other_charges"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Other Description</label>
                            <input type="text" wire:model="other_charges_description"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 shadow-inner focus:border-indigo-600 focus:ring-0 outline-none">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">VAT Applicable?</label>
                            <select wire:model.live="vat_applicable"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">Amount In Words</label>
                            <textarea rows="2" readonly
                                class="flex-1 text-xs bg-indigo-50 border border-indigo-300 px-2.5 py-2 shadow-inner text-indigo-900 font-medium resize-none">{{ $amount_in_words }}</textarea>
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">Terms & Conditions</label>
                            <textarea wire:model="terms_and_conditions" rows="3"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 resize-none"></textarea>
                        </div>

                        <div class="flex items-start w-full xl:col-span-2">
                            <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">Notes</label>
                            <textarea wire:model="notes" rows="2"
                                class="flex-1 text-xs bg-white border border-slate-300 px-2.5 py-1.5 resize-none"></textarea>
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Prepared By</label>
                            <input type="text" wire:model="prepared_by"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Checked By</label>
                            <input type="text" wire:model="checked_by"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5">
                        </div>

                        <div class="flex items-center w-full">
                            <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">Approved By</label>
                            <input type="text" wire:model="approved_by"
                                class="flex-1 text-xs bg-slate-50 border border-slate-300 px-2.5 py-1.5">
                        </div>
                    </div>
                </div>

            </div>

            <div class="xl:col-span-1">
                <div class="border border-slate-300 bg-white shadow-sm overflow-hidden sticky top-6">
                    <div class="bg-slate-800 px-3 py-2 border-b border-slate-900">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-white">
                            Document Snapshot
                        </h2>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="bg-blue-50 border border-blue-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-blue-700">Contract Value</p>
                            <p class="mt-1 text-xl font-black font-mono text-blue-900">
                                {{ number_format((float) $contract_value, 2) }}
                            </p>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-amber-700">Previous Invoices</p>
                            <p class="mt-1 text-xl font-black font-mono text-amber-900">
                                {{ number_format((float) $previous_invoices, 2) }}
                            </p>
                        </div>

                        <div class="bg-purple-50 border border-purple-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-purple-700">Outstanding Before</p>
                            <p class="mt-1 text-xl font-black font-mono text-purple-900">
                                {{ number_format((float) $outstanding_before_invoice, 2) }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-slate-600">Subtotal</p>
                            <p class="mt-1 text-xl font-black font-mono text-slate-900">
                                {{ number_format((float) $subtotal, 2) }}
                            </p>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-amber-700">Taxes / Levies</p>
                            <p class="mt-1 text-xs font-mono text-amber-900">
                                VAT: {{ number_format((float) $vat_amount, 2) }}<br>
                                GETFund: {{ number_format((float) $getfund_amount, 2) }}<br>
                                NHIL: {{ number_format((float) $nhil_amount, 2) }}
                            </p>
                        </div>

                        <div class="bg-indigo-50 border border-indigo-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-indigo-700">Grand Total</p>
                            <p class="mt-1 text-xl font-black font-mono text-indigo-900">
                                {{ number_format((float) $grand_total, 2) }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 p-4">
                            <p class="text-[10px] font-bold uppercase text-slate-600">Balance After</p>
                            <p class="mt-1 text-xl font-black font-mono text-slate-900">
                                {{ number_format((float) $balance_after_invoice, 2) }}
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
                Document Ledger
            </h2>
        </div>

        <div class="w-full bg-slate-200 border-b border-slate-300 flex items-center shadow-inner">
            <span class="pl-4 text-slate-500 font-mono text-sm select-none">🔍</span>
            <input wire:model.live.debounce.500ms="search" type="text"
                placeholder="Filter document records..."
                class="w-full bg-transparent border-0 px-3 py-3 text-xs text-slate-900 placeholder-slate-500 focus:ring-0 outline-none font-medium">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap table-fixed">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300">
                    <tr>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Document No.</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Date</th>
                        <th class="w-56 px-4 py-4 border-r border-slate-200">Client</th>
                        <th class="w-64 px-4 py-4 border-r border-slate-200">Project</th>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Total</th>
                        <th class="w-28 px-4 py-4 border-r border-slate-200">Status</th>
                        <th class="w-64 px-4 py-4">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-indigo-50/70 border-b border-slate-200 transition">
                            <td class="px-4 py-6 font-mono font-bold text-indigo-800 border-r border-slate-200 bg-slate-50/50">
                                {{ $invoice->invoice_number }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono border-r border-slate-200">
                                {{ $invoice->invoice_date }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">{{ $invoice->client_name }}</div>
                                <div class="text-[10px] text-slate-400 truncate mt-0.5">{{ $invoice->client_phone }}</div>
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                {{ $invoice->project_title }}
                            </td>

                            <td class="px-4 py-6 text-indigo-700 font-mono border-r border-slate-200">
                                {{ number_format((float) $invoice->grand_total, 2) }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200">
                                <span class="px-2 py-1 text-[10px] font-bold uppercase border
                                    @if(in_array($invoice->status, ['approved','sent','part_paid','paid','posted']))
                                        bg-green-50 text-green-700 border-green-300
                                    @elseif($invoice->status === 'cancelled')
                                        bg-red-50 text-red-700 border-red-300
                                    @elseif($invoice->status === 'draft')
                                        bg-amber-50 text-amber-700 border-amber-300
                                    @else
                                        bg-slate-50 text-slate-700 border-slate-300
                                    @endif">
                                    {{ strtoupper($invoice->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-6">
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="editInvoice({{ $invoice->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">
                                        Edit
                                    </button>

                                    <button type="button" wire:click="approveInvoice({{ $invoice->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-green-50 text-green-700 border border-green-300">
                                        Approve
                                    </button>

                                    <a href="{{ route('finance.invoices.print', $invoice) }}" target="_blank"
                                        class="px-2 py-1 text-[10px] font-bold bg-slate-50 text-slate-700 border border-slate-300">
                                        Print
                                    </a>

                                    <button type="button" wire:click="postInvoiceToLedger({{ $invoice->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-300">
                                        Post GL
                                    </button>

                                    <button type="button" wire:click="cancelInvoice({{ $invoice->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">
                                        Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                [Err] 0 document records returned.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>