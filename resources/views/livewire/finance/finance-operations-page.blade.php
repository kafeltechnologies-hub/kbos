<div class="min-h-screen bg-slate-100 p-6 text-slate-900">

@include('livewire.finance._header', [
    'title' => 'Finance Operations Centre',
    'subtitle' => 'Sales, receipts, payments, banking and workflow control. Approved items move to General Ledger for posting.'
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

<div class="mb-6 overflow-x-auto">
    <div class="flex gap-4 min-w-max">
        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Today Receipts</p>
            <p class="text-2xl font-black font-mono text-green-700">{{ number_format($todayReceipts ?? 0, 2) }}</p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Today Payments</p>
            <p class="text-2xl font-black font-mono text-red-700">{{ number_format($todayPayments ?? 0, 2) }}</p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Net Movement</p>
            <p class="text-2xl font-black font-mono {{ (($todayNetMovement ?? 0) >= 0) ? 'text-green-700' : 'text-red-700' }}">
                {{ number_format($todayNetMovement ?? 0, 2) }}
            </p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Pending Approval</p>
            <p class="text-2xl font-black font-mono text-purple-700">{{ $pendingApprovals ?? 0 }}</p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Drafts</p>
            <p class="text-2xl font-black font-mono text-slate-700">{{ $draftTransactions ?? 0 }}</p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Approved For GL</p>
            <p class="text-2xl font-black font-mono text-blue-700">{{ $approvedTransactions ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="bg-white border border-slate-300 mb-6 overflow-x-auto">
    <div class="flex gap-2 p-3 bg-slate-100 min-w-max">
        @foreach([
            'home' => 'Operations Home',
            'quotation' => 'New Quotation',
            'invoice' => 'New Invoice',
            'convert' => 'Convert Quote',
            'receipt' => 'Receipts',
            'loan' => 'Loans / Capital',
            'payment' => 'Payments',
            'transfer' => 'Banking',
            'transactions' => 'Workflow',
        ] as $tab => $label)
            <button type="button"
                    wire:click="go('{{ $tab }}')"
                    class="px-4 py-2 text-xs font-bold border {{ $activeTab === $tab ? 'bg-green-700 text-white border-green-800' : 'bg-white border-slate-300 hover:bg-slate-50' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>

@if($activeTab === 'home')
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-5">
        <div class="bg-white border border-slate-300 shadow-sm">
            <div class="bg-slate-900 text-white px-4 py-3">
                <h2 class="text-sm font-black">Sales</h2>
            </div>
            <div class="p-4 space-y-3">
                <button type="button" wire:click="go('quotation')" class="w-full text-left border border-slate-300 p-4 hover:bg-green-50">
                    <p class="font-black text-sm">New Quotation</p>
                    <p class="text-xs text-slate-500">Prepare customer quotation.</p>
                </button>
                <button type="button" wire:click="go('invoice')" class="w-full text-left border border-slate-300 p-4 hover:bg-blue-50">
                    <p class="font-black text-sm">New Invoice</p>
                    <p class="text-xs text-slate-500">Create invoice directly.</p>
                </button>
                <button type="button" wire:click="go('convert')" class="w-full text-left border border-slate-300 p-4 hover:bg-purple-50">
                    <p class="font-black text-sm">Convert Quote to Invoice</p>
                    <p class="text-xs text-slate-500">Generate invoice from quotation.</p>
                </button>
            </div>
        </div>

        <div class="bg-white border border-slate-300 shadow-sm">
            <div class="bg-slate-900 text-white px-4 py-3">
                <h2 class="text-sm font-black">Receipts</h2>
            </div>
            <div class="p-4 space-y-3">
                <button type="button" wire:click="go('receipt')" class="w-full text-left border border-slate-300 p-4 hover:bg-emerald-50">
                    <p class="font-black text-sm">Customer Receipt</p>
                    <p class="text-xs text-slate-500">Receive against invoice or income.</p>
                </button>
                <button type="button" wire:click="go('loan')" class="w-full text-left border border-slate-300 p-4 hover:bg-indigo-50">
                    <p class="font-black text-sm">Loan Receipt</p>
                    <p class="text-xs text-slate-500">Bank, director or shareholder loan.</p>
                </button>
                <button type="button" wire:click="go('loan')" class="w-full text-left border border-slate-300 p-4 hover:bg-indigo-50">
                    <p class="font-black text-sm">Capital Injection</p>
                    <p class="text-xs text-slate-500">Record capital contribution.</p>
                </button>
                <button type="button" wire:click="go('receipt')" class="w-full text-left border border-slate-300 p-4 hover:bg-emerald-50">
                    <p class="font-black text-sm">Other Income</p>
                    <p class="text-xs text-slate-500">Refunds, grants, claims, rent.</p>
                </button>
            </div>
        </div>

        <div class="bg-white border border-slate-300 shadow-sm">
            <div class="bg-slate-900 text-white px-4 py-3">
                <h2 class="text-sm font-black">Payments</h2>
            </div>
            <div class="p-4 space-y-3">
                @foreach([
                    'Supplier' => 'Supplier bills and advances.',
                    'Payroll' => 'Salary, allowance and statutory payroll.',
                    'Project' => 'Project-related expenditure.',
                    'Operations' => 'Fuel, utilities, repairs, office cost.',
                    'Tax' => 'VAT, PAYE, WHT, SSNIT and other taxes.',
                    'Asset Purchase' => 'Vehicles, equipment and fixed assets.',
                ] as $label => $desc)
                    <button type="button" wire:click="go('payment')" class="w-full text-left border border-slate-300 p-4 hover:bg-red-50">
                        <p class="font-black text-sm">{{ $label }}</p>
                        <p class="text-xs text-slate-500">{{ $desc }}</p>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="bg-white border border-slate-300 shadow-sm">
            <div class="bg-slate-900 text-white px-4 py-3">
                <h2 class="text-sm font-black">Banking</h2>
            </div>
            <div class="p-4 space-y-3">
                @foreach([
                    'Transfer Funds' => 'Move funds between accounts.',
                    'Petty Cash' => 'Petty cash float or replenishment.',
                    'Mobile Money' => 'Bank to momo or momo to bank.',
                    'Bank Deposits' => 'Cash to bank deposit.',
                ] as $label => $desc)
                    <button type="button" wire:click="go('transfer')" class="w-full text-left border border-slate-300 p-4 hover:bg-amber-50">
                        <p class="font-black text-sm">{{ $label }}</p>
                        <p class="text-xs text-slate-500">{{ $desc }}</p>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="bg-white border border-slate-300 shadow-sm">
            <div class="bg-slate-900 text-white px-4 py-3">
                <h2 class="text-sm font-black">Workflow</h2>
            </div>
            <div class="p-4 space-y-3">
                <button type="button" wire:click="go('transactions')" class="w-full text-left border border-slate-300 p-4 hover:bg-slate-50">
                    <p class="font-black text-sm">Approval Queue</p>
                    <p class="text-xs text-slate-500">Draft and submitted transactions.</p>
                </button>
                <button type="button" wire:click="go('transactions')" class="w-full text-left border border-slate-300 p-4 hover:bg-slate-50">
                    <p class="font-black text-sm">Recent Transactions</p>
                    <p class="text-xs text-slate-500">Review finance activity.</p>
                </button>
                <button type="button" wire:click="go('transactions')" class="w-full text-left border border-slate-300 p-4 hover:bg-slate-50">
                    <p class="font-black text-sm">Print Documents</p>
                    <p class="text-xs text-slate-500">Print approved or draft records.</p>
                </button>
            </div>
        </div>
    </div>
@endif

@if($activeTab === 'quotation' || $activeTab === 'invoice' || $activeTab === 'convert')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">
                    {{ $document_type === 'quotation' ? 'Sales / Quotation' : 'Sales / Invoice' }}
                </p>
                <h2 class="text-sm font-black">
                    @if($activeTab === 'convert') Convert Quote to Invoice
                    @elseif($document_type === 'quotation') New Quotation
                    @else New Invoice
                    @endif
                </h2>
            </div>

            <form wire:submit.prevent="saveDocument" class="p-5 space-y-4">
                @if($activeTab === 'convert')
                    <div>
                        <label class="text-xs font-bold text-slate-700">Select Quotation</label>
                        <select wire:model.live="source_quotation_no" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">Select quotation to convert</option>
                            @foreach($quotationOptions ?? [] as $quote)
                                <option value="{{ $quote->document_no }}">
                                    {{ $quote->document_no }} — {{ $quote->customer_name }} — {{ number_format((float)$quote->grand_total, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <input type="hidden" wire:model="document_type">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-700">Document Date</label>
                        <input type="date" wire:model="document_date" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700">Document Number</label>
                        <input type="text" wire:model="document_no" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700">Party / Customer</label>
                    <select wire:model.live="party_id" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                        <option value="">Select party / customer</option>
                        @foreach($parties ?? [] as $party)
                            <option value="{{ $party->id }}">{{ $party->party_code }} — {{ $party->name }} ({{ $party->party_type }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700">Customer Name</label>
                    <input type="text" wire:model="customer_name" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700">Project</label>
                    <select wire:model="project_id" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                        <option value="">No Project</option>
                        @foreach($projects ?? [] as $project)
                            <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700">Service Description / Scope of Work</label>
                    <textarea wire:model="service_description" rows="3" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs resize-none"></textarea>
                </div>

                <div class="border border-slate-300">
                    <div class="bg-slate-100 px-3 py-2 flex justify-between">
                        <p class="text-xs font-black">Document Lines</p>
                        <button type="button" wire:click="addLine" class="text-[10px] font-bold text-green-700">+ Add Line</button>
                    </div>

                    <div class="p-3 space-y-3">
                        @foreach($documentLines as $index => $line)
                            <div class="bg-slate-50 border border-slate-200 p-3 space-y-2">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600">Line Type</label>
                                        <select wire:model.live="documentLines.{{ $index }}.line_type" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                                            <option value="material">Material</option>
                                            <option value="service">Service</option>
                                            <option value="labour">Labour</option>
                                            <option value="transport">Transport</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600">Material</label>
                                        <select wire:model.live="documentLines.{{ $index }}.material_id" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                                            <option value="">No material</option>
                                            @foreach($materials ?? [] as $material)
                                                <option value="{{ $material->id }}">{{ $material->material_code }} — {{ $material->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[10px] font-bold text-slate-600">Description</label>
                                    <input type="text" wire:model="documentLines.{{ $index }}.description" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs">
                                </div>

                                <div class="grid grid-cols-4 gap-2">
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600">Unit</label>
                                        <input type="text" wire:model="documentLines.{{ $index }}.unit" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600">Quantity</label>
                                        <input type="number" step="0.01" wire:model.live="documentLines.{{ $index }}.quantity" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600">Unit Price</label>
                                        <input type="number" step="0.01" wire:model.live="documentLines.{{ $index }}.unit_price" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600">Amount</label>
                                        <input type="number" step="0.01" wire:model="documentLines.{{ $index }}.amount" readonly class="mt-1 w-full border border-slate-300 bg-slate-100 px-2 py-2 text-xs">
                                    </div>
                                </div>

                                <button type="button" wire:click="removeLine({{ $index }})" class="text-[10px] font-bold text-red-700">Remove Line</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-700">Labour Cost</label>
                        <input type="number" step="0.01" wire:model.live="labour_cost" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700">Transport Cost</label>
                        <input type="number" step="0.01" wire:model.live="transport_cost" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700">Other Cost</label>
                        <input type="number" step="0.01" wire:model.live="other_cost" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-700">Discount Amount</label>
                        <input type="number" step="0.01" wire:model.live="discount_amount" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700">Tax Rate %</label>
                        <input type="number" step="0.01" wire:model.live="tax_rate" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700">Narration / Terms</label>
                    <textarea wire:model="document_narration" rows="3" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                    <div class="bg-slate-50 border p-3">
                        <p class="text-[10px] font-bold text-slate-500">Lines</p>
                        <p class="font-mono font-black">{{ number_format($this->materialsTotal, 2) }}</p>
                    </div>
                    <div class="bg-slate-50 border p-3">
                        <p class="text-[10px] font-bold text-slate-500">Other</p>
                        <p class="font-mono font-black">{{ number_format($this->otherTotal, 2) }}</p>
                    </div>
                    <div class="bg-slate-50 border p-3">
                        <p class="text-[10px] font-bold text-slate-500">Subtotal</p>
                        <p class="font-mono font-black">{{ number_format($this->subTotal, 2) }}</p>
                    </div>
                    <div class="bg-slate-50 border p-3">
                        <p class="text-[10px] font-bold text-slate-500">Tax</p>
                        <p class="font-mono font-black text-red-700">{{ number_format($this->taxAmount, 2) }}</p>
                    </div>
                    <div class="bg-green-50 border border-green-300 p-3">
                        <p class="text-[10px] font-bold text-green-700">Grand Total</p>
                        <p class="font-mono font-black text-green-800">{{ number_format($this->grandTotal, 2) }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="bg-green-700 text-white px-4 py-2 text-xs font-bold">
                        {{ $editingDocumentId ? 'Update Draft' : 'Save Draft' }}
                    </button>

                    <button type="button" wire:click="submitDocument" class="bg-blue-700 text-white px-4 py-2 text-xs font-bold">
                        Submit
                    </button>

                    <button type="button" wire:click="clearDocumentForm" class="bg-white border border-slate-300 px-4 py-2 text-xs font-bold">
                        Clear
                    </button>

                    @if($document_type === 'quotation' && $document_no && Route::has('finance.operations.quotation.print'))
                        <a target="_blank" href="{{ route('finance.operations.quotation.print', $document_no) }}" class="bg-slate-900 text-white px-4 py-2 text-xs font-bold">Print</a>
                    @endif

                    @if($document_type === 'invoice' && $document_no && Route::has('finance.operations.invoice.print'))
                        <a target="_blank" href="{{ route('finance.operations.invoice.print', $document_no) }}" class="bg-slate-900 text-white px-4 py-2 text-xs font-bold">Print</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="xl:col-span-2 bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <h2 class="text-sm font-black">{{ $document_type === 'quotation' ? 'Recent Quotations' : 'Recent Invoices' }}</h2>
            </div>

            <div class="divide-y divide-slate-200">
                @php
                    $listDocuments = $document_type === 'quotation' ? ($quotations ?? collect()) : ($invoices ?? collect());
                @endphp

                @forelse($listDocuments as $doc)
                    @php
                        $locked = ! in_array($doc->status, ['draft', 'submitted'], true);
                        $rowClass = $locked ? 'bg-slate-100 opacity-70' : 'bg-white';
                    @endphp

                    <div class="p-4 flex flex-col xl:flex-row xl:justify-between gap-4 {{ $rowClass }}">
                        <div>
                            <p class="font-mono font-black text-green-700">{{ $doc->document_no }}</p>
                            <p class="text-xs font-bold">{{ $doc->customer_name }}</p>
                            <p class="text-[10px] text-slate-500">
                                {{ strtoupper($doc->document_type) }} | {{ strtoupper($doc->status) }} | {{ $doc->document_date?->format('d M Y') ?? '-' }}
                            </p>
                            @if($locked)
                                <p class="text-[10px] font-bold text-slate-500 mt-1">Locked: approved/processed records are no longer editable here.</p>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <div class="bg-slate-50 border px-3 py-2">
                                <p class="text-[10px] font-bold">Total</p>
                                <p class="font-mono font-black">{{ number_format((float)$doc->grand_total, 2) }}</p>
                            </div>

                            @if(!$locked)
                                <button type="button" wire:click="editDocument({{ $doc->id }})" class="bg-blue-50 text-blue-700 border border-blue-300 px-3 py-2 text-[10px] font-bold">Edit</button>
                                <button type="button" wire:click="approveDocument({{ $doc->id }})" class="bg-green-50 text-green-700 border border-green-300 px-3 py-2 text-[10px] font-bold">Approve</button>
                                <button type="button" wire:click="cancelDocument({{ $doc->id }})" onclick="return confirm('Cancel this document?')" class="bg-orange-50 text-orange-700 border border-orange-300 px-3 py-2 text-[10px] font-bold">Cancel</button>
                                <button type="button" wire:click="deleteDocument({{ $doc->id }})" onclick="return confirm('Delete this document?')" class="bg-red-50 text-red-700 border border-red-300 px-3 py-2 text-[10px] font-bold">Delete</button>
                            @else
                                <span class="bg-slate-200 text-slate-500 border border-slate-300 px-3 py-2 text-[10px] font-bold">Locked</span>
                            @endif

                            <button type="button" wire:click="duplicateDocument({{ $doc->id }})" class="bg-slate-50 text-slate-700 border border-slate-300 px-3 py-2 text-[10px] font-bold">Duplicate</button>

                            @if($doc->status !== 'posted')
                                <button type="button" wire:click="reverseDocument({{ $doc->id }})" onclick="return confirm('Reverse this document?')" class="bg-amber-50 text-amber-700 border border-amber-300 px-3 py-2 text-[10px] font-bold">Reverse</button>
                            @endif

                            @if($doc->document_type === 'quotation' && Route::has('finance.operations.quotation.print'))
                                <a target="_blank" href="{{ route('finance.operations.quotation.print', $doc->document_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                            @endif

                            @if($doc->document_type === 'invoice' && Route::has('finance.operations.invoice.print'))
                                <a target="_blank" href="{{ route('finance.operations.invoice.print', $doc->document_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 font-bold">No records found.</div>
                @endforelse
            </div>
        </div>
    </div>
@endif

@if($activeTab === 'receipt' || $activeTab === 'payment' || $activeTab === 'transfer' || $activeTab === 'loan')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">
                    @if($transaction_type === 'receipt') Receipts
                    @elseif($transaction_type === 'loan') Loans / Capital
                    @elseif($transaction_type === 'transfer') Banking
                    @else Payments
                    @endif
                </p>
                <h2 class="text-sm font-black">
                    @if($transaction_type === 'receipt') Receive Funds
                    @elseif($transaction_type === 'loan') Receive Loan / Capital
                    @elseif($transaction_type === 'transfer') Transfer Funds
                    @else Make Payment
                    @endif
                </h2>
            </div>

            <form wire:submit.prevent="saveTransaction" class="p-5 space-y-4">
                <input type="hidden" wire:model="transaction_type">

                @if($transaction_type === 'receipt')
                    <div>
                        <label class="text-xs font-bold text-slate-700">Related Invoice</label>
                        <select wire:model.live="finance_document_id" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">No invoice / General receipt</option>
                            @foreach($invoiceOptions ?? [] as $invoice)
                                <option value="{{ $invoice->id }}">
                                    {{ $invoice->document_no }} — {{ $invoice->customer_name }} — {{ number_format((float)$invoice->grand_total, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-700">Transaction Date</label>
                        <input type="date" wire:model="reference_date" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700">Reference Number</label>
                        <input type="text" wire:model="reference_no" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>
                </div>

                @if($transaction_type !== 'transfer')
                    <div>
                        <label class="text-xs font-bold text-slate-700">Party</label>
                        <select wire:model.live="party_id" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">Select party</option>
                            @foreach($parties ?? [] as $party)
                                <option value="{{ $party->id }}">{{ $party->party_code }} — {{ $party->name }} ({{ $party->party_type }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($transaction_type === 'receipt')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-700">Receipt Type</label>
                            <select wire:model="transaction_subtype" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                                <option value="invoice_payment">Customer Receipt / Invoice Payment</option>
                                <option value="advance_payment">Customer Advance</option>
                                <option value="deposit">Deposit</option>
                                <option value="retention_release">Retention Release</option>
                                <option value="misc_income">Other Income</option>
                                <option value="rental_income">Rental Income</option>
                                <option value="service_income">Service Income</option>
                                <option value="refund_received">Refund Received</option>
                                <option value="insurance_claim">Insurance Claim</option>
                                <option value="interest_income">Interest Income</option>
                                <option value="grant">Grant</option>
                                <option value="donation">Donation</option>
                                <option value="scrap_sale">Scrap Sale</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700">Received From</label>
                            <input type="text" wire:model="party_name" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>
                    </div>
                @elseif($transaction_type === 'loan')
                    <div>
                        <label class="text-xs font-bold text-slate-700">Lender / Capital Provider</label>
                        <input type="text" wire:model="lender_name" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-700">Loan / Capital Type</label>
                            <select wire:model="transaction_subtype" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                                <option value="bank_loan">Bank Loan</option>
                                <option value="director_loan">Director Loan</option>
                                <option value="shareholder_loan">Shareholder Loan</option>
                                <option value="external_financing">External Financing</option>
                                <option value="capital_injection">Capital Injection</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700">Interest Period</label>
                            <select wire:model="interest_period" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                                <option value="monthly">Interest Per Month</option>
                                <option value="annual">Interest Per Annum</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-700">Interest Rate %</label>
                            <input type="number" step="0.01" wire:model="interest_rate" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700">Start Date</label>
                            <input type="date" wire:model="loan_start_date" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700">Due Date</label>
                            <input type="date" wire:model="loan_due_date" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>
                    </div>
                @elseif($transaction_type === 'payment')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-700">Payment Type</label>
                            <select wire:model="transaction_subtype" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                                <option value="supplier_payment">Supplier</option>
                                <option value="payroll_salary">Payroll</option>
                                <option value="project_expense">Project</option>
                                <option value="operations">Operations</option>
                                <option value="vat">Tax - VAT</option>
                                <option value="paye">Tax - PAYE</option>
                                <option value="ssnit">Tax - SSNIT</option>
                                <option value="wht">Tax - WHT</option>
                                <option value="asset_purchase">Asset Purchase</option>
                                <option value="inventory_purchase">Inventory Purchase</option>
                                <option value="supplier_advance">Supplier Advance</option>
                                <option value="fuel">Fuel</option>
                                <option value="utilities">Utilities</option>
                                <option value="repairs">Repairs & Maintenance</option>
                                <option value="professional_fees">Professional Fees</option>
                                <option value="travel">Travel</option>
                                <option value="refund_customer">Customer Refund</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700">Payee / Supplier / Staff</label>
                            <input type="text" wire:model="party_name" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                        </div>
                    </div>
                @elseif($transaction_type === 'transfer')
                    <div>
                        <label class="text-xs font-bold text-slate-700">Banking Type</label>
                        <select wire:model="transaction_subtype" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="bank_to_cash">Transfer Funds - Bank to Cash</option>
                            <option value="cash_to_bank">Bank Deposit - Cash to Bank</option>
                            <option value="bank_to_momo">Mobile Money - Bank to Momo</option>
                            <option value="momo_to_bank">Mobile Money - Momo to Bank</option>
                            <option value="bank_to_bank">Transfer Funds - Bank to Bank</option>
                            <option value="petty_cash_float">Petty Cash Float</option>
                            <option value="cash_replenishment">Petty Cash Replenishment</option>
                        </select>
                    </div>
                @endif

                @if($transaction_type !== 'transfer' && $transaction_type !== 'loan')
                    <div>
                        <label class="text-xs font-bold text-slate-700">Project</label>
                        <select wire:model="transaction_project_id" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">No Project</option>
                            @foreach($projects ?? [] as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700">Budget</label>
                        <select wire:model="budget_id" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">No Budget</option>
                            @foreach($budgets ?? [] as $budget)
                                <option value="{{ $budget->id }}">{{ $budget->budget_code ?? 'BUD-'.$budget->id }} — {{ $budget->budget_name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($transaction_type === 'transfer')
                    <div>
                        <label class="text-xs font-bold text-slate-700">From Account / Wallet</label>
                        <select wire:model="from_account_id" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">Select source account</option>
                            @foreach($cashAccounts ?? [] as $account)
                                <option value="{{ $account->id }}">{{ $account->account_code }} — {{ $account->account_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700">To Account / Wallet</label>
                        <select wire:model="to_account_id" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">Select destination account</option>
                            @foreach($cashAccounts ?? [] as $account)
                                <option value="{{ $account->id }}">{{ $account->account_code }} — {{ $account->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div>
                        <label class="text-xs font-bold text-slate-700">Cash / Bank / Momo Account</label>
                        <select wire:model="cash_account_id" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">Select receiving or paying account</option>
                            @foreach($cashAccounts ?? [] as $account)
                                <option value="{{ $account->id }}">{{ $account->account_code }} — {{ $account->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-700">Gross Amount</label>
                        <input type="number" step="0.01" wire:model.live="gross_amount" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700">Payment Method</label>
                        <select wire:model="payment_method" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="cash">Cash</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="momo">Mobile Money</option>
                            <option value="card">Card</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-700">Discount Amount</label>
                        <input type="number" step="0.01" wire:model.live="discount_transaction_amount" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700">Tax Amount</label>
                        <input type="number" step="0.01" wire:model.live="tax_transaction_amount" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700">WHT Amount</label>
                        <input type="number" step="0.01" wire:model.live="wht_amount" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-300 p-3">
                    <p class="text-[10px] uppercase font-bold text-slate-500">Net Amount</p>
                    <p class="font-mono text-xl font-black text-green-700">{{ number_format((float)$net_amount, 2) }}</p>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700">External Reference</label>
                    <input type="text" wire:model="external_reference" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700">Narration</label>
                    <textarea wire:model="transaction_narration" rows="3" class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs resize-none"></textarea>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="bg-green-700 text-white px-4 py-2 text-xs font-bold">
                        {{ $editingTransactionId ? 'Update Draft' : 'Save Draft' }}
                    </button>

                    <button type="button" wire:click="submitTransaction" class="bg-blue-700 text-white px-4 py-2 text-xs font-bold">Submit</button>

                    <button type="button" wire:click="clearTransactionForm" class="bg-white border border-slate-300 px-4 py-2 text-xs font-bold">Clear</button>

                    @if($reference_no && $transaction_type === 'receipt' && Route::has('finance.operations.receipt.print'))
                        <a target="_blank" href="{{ route('finance.operations.receipt.print', $reference_no) }}" class="bg-slate-900 text-white px-4 py-2 text-xs font-bold">Print</a>
                    @endif

                    @if($reference_no && $transaction_type === 'payment' && Route::has('finance.operations.payment.print'))
                        <a target="_blank" href="{{ route('finance.operations.payment.print', $reference_no) }}" class="bg-slate-900 text-white px-4 py-2 text-xs font-bold">Print</a>
                    @endif

                    @if($reference_no && $transaction_type === 'loan' && Route::has('finance.operations.loan.print'))
                        <a target="_blank" href="{{ route('finance.operations.loan.print', $reference_no) }}" class="bg-slate-900 text-white px-4 py-2 text-xs font-bold">Print</a>
                    @endif

                    @if($reference_no && $transaction_type === 'transfer' && Route::has('finance.operations.transfer.print'))
                        <a target="_blank" href="{{ route('finance.operations.transfer.print', $reference_no) }}" class="bg-slate-900 text-white px-4 py-2 text-xs font-bold">Print</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="xl:col-span-2 bg-white border border-slate-300 shadow-sm overflow-hidden">
            <div class="bg-slate-900 text-white px-4 py-3">
                <h2 class="text-sm font-black">
                    @if($transaction_type === 'receipt') Recent Receipts
                    @elseif($transaction_type === 'loan') Recent Loans / Capital
                    @elseif($transaction_type === 'transfer') Recent Banking Transactions
                    @else Recent Payments
                    @endif
                </h2>
            </div>

            <div class="divide-y divide-slate-200">
                @php
                    $listTransactions = match($transaction_type) {
                        'receipt' => $receipts ?? collect(),
                        'loan' => $loans ?? collect(),
                        'transfer' => $transfers ?? collect(),
                        default => $paymentVouchers ?? collect(),
                    };
                @endphp

                @forelse($listTransactions as $txn)
                    @php
                        $locked = ! in_array($txn->status, ['draft', 'submitted'], true);
                        $rowClass = $locked ? 'bg-slate-100 opacity-70' : 'bg-white';
                    @endphp

                    <div class="p-4 flex flex-col xl:flex-row xl:justify-between gap-4 {{ $rowClass }}">
                        <div>
                            <p class="font-mono font-black text-green-700">{{ $txn->reference_no }}</p>
                            <p class="text-xs font-bold">{{ $txn->party_name }}</p>
                            <p class="text-[10px] text-slate-500">
                                {{ strtoupper($txn->transaction_type) }} | {{ strtoupper($txn->status) }} | {{ $txn->reference_date?->format('d M Y') ?? '-' }}
                            </p>
                            @if($locked)
                                <p class="text-[10px] font-bold text-slate-500 mt-1">Locked: approved/processed records are no longer editable here.</p>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <div class="bg-slate-50 border px-3 py-2">
                                <p class="text-[10px] font-bold">Amount</p>
                                <p class="font-mono font-black">{{ number_format((float)$txn->gross_amount, 2) }}</p>
                            </div>

                            @if(!$locked)
                                <button type="button" wire:click="editTransaction({{ $txn->id }})" class="bg-blue-50 text-blue-700 border border-blue-300 px-3 py-2 text-[10px] font-bold">Edit</button>
                                <button type="button" wire:click="approveTransaction({{ $txn->id }})" class="bg-green-50 text-green-700 border border-green-300 px-3 py-2 text-[10px] font-bold">Approve</button>
                                <button type="button" wire:click="cancelTransaction({{ $txn->id }})" onclick="return confirm('Cancel this transaction?')" class="bg-orange-50 text-orange-700 border border-orange-300 px-3 py-2 text-[10px] font-bold">Cancel</button>
                                <button type="button" wire:click="deleteTransaction({{ $txn->id }})" onclick="return confirm('Delete this transaction?')" class="bg-red-50 text-red-700 border border-red-300 px-3 py-2 text-[10px] font-bold">Delete</button>
                            @else
                                <span class="bg-slate-200 text-slate-500 border border-slate-300 px-3 py-2 text-[10px] font-bold">Locked</span>
                            @endif

                            <button type="button" wire:click="duplicateTransaction({{ $txn->id }})" class="bg-slate-50 text-slate-700 border border-slate-300 px-3 py-2 text-[10px] font-bold">Duplicate</button>

                            @if($txn->status !== 'posted')
                                <button type="button" wire:click="reverseTransaction({{ $txn->id }})" onclick="return confirm('Reverse this transaction?')" class="bg-amber-50 text-amber-700 border border-amber-300 px-3 py-2 text-[10px] font-bold">Reverse</button>
                            @endif

                            @if($txn->transaction_type === 'receipt' && Route::has('finance.operations.receipt.print'))
                                <a target="_blank" href="{{ route('finance.operations.receipt.print', $txn->reference_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                            @endif

                            @if($txn->transaction_type === 'payment' && Route::has('finance.operations.payment.print'))
                                <a target="_blank" href="{{ route('finance.operations.payment.print', $txn->reference_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                            @endif

                            @if($txn->transaction_type === 'loan' && Route::has('finance.operations.loan.print'))
                                <a target="_blank" href="{{ route('finance.operations.loan.print', $txn->reference_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                            @endif

                            @if($txn->transaction_type === 'transfer' && Route::has('finance.operations.transfer.print'))
                                <a target="_blank" href="{{ route('finance.operations.transfer.print', $txn->reference_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 font-bold">No records found.</div>
                @endforelse
            </div>
        </div>
    </div>
@endif

@if($activeTab === 'transactions')
    <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
        <div class="bg-slate-900 text-white px-4 py-3">
            <h2 class="text-sm font-black">Workflow Centre</h2>
            <p class="text-[10px] text-slate-300 mt-1">
                Approval Queue, Recent Transactions and Print Documents. Queue Only shows Draft and Submitted records.
            </p>
        </div>

        <div class="bg-slate-50 border-b p-4 overflow-x-auto">
            <div class="flex gap-3 min-w-max">
                <div>
                    <label class="text-[10px] font-bold text-slate-600">Search</label>
                    <input type="text" wire:model.live.debounce.500ms="search" class="mt-1 w-72 border border-slate-300 px-3 py-2 text-xs">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600">From Date</label>
                    <input type="date" wire:model.live="date_from" class="mt-1 w-44 border border-slate-300 px-3 py-2 text-xs">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600">To Date</label>
                    <input type="date" wire:model.live="date_to" class="mt-1 w-44 border border-slate-300 px-3 py-2 text-xs">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600">Transaction Type</label>
                    <select wire:model.live="type_filter" class="mt-1 w-48 border border-slate-300 px-3 py-2 text-xs bg-white">
                        <option value="">All Types</option>
                        <option value="quotation">Quotation</option>
                        <option value="invoice">Invoice</option>
                        <option value="receipt">Receipt</option>
                        <option value="payment">Payment</option>
                        <option value="transfer">Transfer</option>
                        <option value="loan">Loan</option>
                        <option value="capital">Capital</option>
                        <option value="refund">Refund</option>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600">Workflow Status</label>
                    <select wire:model.live="status_filter" class="mt-1 w-48 border border-slate-300 px-3 py-2 text-xs bg-white">
                        <option value="">Queue Only</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Submitted</option>
                        <option value="approved">Approved</option>
                        <option value="posted">Posted</option>
                        <option value="reversed">Reversed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="button" wire:click="clearFilters" class="bg-white border border-slate-300 px-4 py-2 text-xs font-bold">Clear</button>
                </div>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-b">
            <p class="text-xs font-black uppercase">Sales Documents</p>
        </div>

        <div class="divide-y divide-slate-200">
            @forelse($documents ?? [] as $doc)
                @php
                    $locked = ! in_array($doc->status, ['draft', 'submitted'], true);
                    $rowClass = $locked ? 'bg-slate-100 opacity-70' : 'bg-white';
                @endphp

                <div class="p-4 flex flex-col xl:flex-row xl:justify-between gap-4 {{ $rowClass }}">
                    <div>
                        <p class="font-mono font-black text-green-700">{{ $doc->document_no }}</p>
                        <p class="text-xs font-bold">{{ $doc->customer_name }}</p>
                        <p class="text-[10px] text-slate-500">
                            {{ strtoupper($doc->document_type) }} | {{ strtoupper($doc->status) }} | {{ $doc->document_date?->format('d M Y') ?? '-' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <div class="bg-slate-50 border px-3 py-2">
                            <p class="text-[10px] font-bold">Total</p>
                            <p class="font-mono font-black">{{ number_format((float)$doc->grand_total, 2) }}</p>
                        </div>

                        @if(!$locked)
                            <button type="button" wire:click="editDocument({{ $doc->id }})" class="bg-blue-50 text-blue-700 border border-blue-300 px-3 py-2 text-[10px] font-bold">Edit</button>
                            <button type="button" wire:click="approveDocument({{ $doc->id }})" class="bg-green-50 text-green-700 border border-green-300 px-3 py-2 text-[10px] font-bold">Approve</button>
                            <button type="button" wire:click="cancelDocument({{ $doc->id }})" onclick="return confirm('Cancel this document?')" class="bg-orange-50 text-orange-700 border border-orange-300 px-3 py-2 text-[10px] font-bold">Cancel</button>
                            <button type="button" wire:click="deleteDocument({{ $doc->id }})" onclick="return confirm('Delete this document?')" class="bg-red-50 text-red-700 border border-red-300 px-3 py-2 text-[10px] font-bold">Delete</button>
                        @else
                            <span class="bg-slate-200 text-slate-500 border border-slate-300 px-3 py-2 text-[10px] font-bold">Locked</span>
                        @endif

                        <button type="button" wire:click="duplicateDocument({{ $doc->id }})" class="bg-slate-50 text-slate-700 border border-slate-300 px-3 py-2 text-[10px] font-bold">Duplicate</button>

                        @if($doc->document_type === 'quotation' && Route::has('finance.operations.quotation.print'))
                            <a target="_blank" href="{{ route('finance.operations.quotation.print', $doc->document_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                        @endif

                        @if($doc->document_type === 'invoice' && Route::has('finance.operations.invoice.print'))
                            <a target="_blank" href="{{ route('finance.operations.invoice.print', $doc->document_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 font-bold">No document approvals waiting.</div>
            @endforelse
        </div>

        <div class="p-4 bg-slate-50 border-y">
            <p class="text-xs font-black uppercase">Receipts, Payments, Loans & Banking</p>
        </div>

        <div class="divide-y divide-slate-200">
            @forelse($transactions ?? [] as $txn)
                @php
                    $locked = ! in_array($txn->status, ['draft', 'submitted'], true);
                    $rowClass = $locked ? 'bg-slate-100 opacity-70' : 'bg-white';
                @endphp

                <div class="p-4 flex flex-col xl:flex-row xl:justify-between gap-4 {{ $rowClass }}">
                    <div>
                        <p class="font-mono font-black text-green-700">{{ $txn->reference_no }}</p>
                        <p class="text-xs font-bold">{{ $txn->party_name }}</p>
                        <p class="text-[10px] text-slate-500">
                            {{ strtoupper($txn->transaction_type) }} | {{ strtoupper($txn->status) }} | {{ $txn->reference_date?->format('d M Y') ?? '-' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <div class="bg-slate-50 border px-3 py-2">
                            <p class="text-[10px] font-bold">Amount</p>
                            <p class="font-mono font-black">{{ number_format((float)$txn->gross_amount, 2) }}</p>
                        </div>

                        @if(!$locked)
                            <button type="button" wire:click="editTransaction({{ $txn->id }})" class="bg-blue-50 text-blue-700 border border-blue-300 px-3 py-2 text-[10px] font-bold">Edit</button>
                            <button type="button" wire:click="approveTransaction({{ $txn->id }})" class="bg-green-50 text-green-700 border border-green-300 px-3 py-2 text-[10px] font-bold">Approve</button>
                            <button type="button" wire:click="cancelTransaction({{ $txn->id }})" onclick="return confirm('Cancel this transaction?')" class="bg-orange-50 text-orange-700 border border-orange-300 px-3 py-2 text-[10px] font-bold">Cancel</button>
                            <button type="button" wire:click="deleteTransaction({{ $txn->id }})" onclick="return confirm('Delete this transaction?')" class="bg-red-50 text-red-700 border border-red-300 px-3 py-2 text-[10px] font-bold">Delete</button>
                        @else
                            <span class="bg-slate-200 text-slate-500 border border-slate-300 px-3 py-2 text-[10px] font-bold">Locked</span>
                        @endif

                        <button type="button" wire:click="duplicateTransaction({{ $txn->id }})" class="bg-slate-50 text-slate-700 border border-slate-300 px-3 py-2 text-[10px] font-bold">Duplicate</button>

                        @if($txn->transaction_type === 'receipt' && Route::has('finance.operations.receipt.print'))
                            <a target="_blank" href="{{ route('finance.operations.receipt.print', $txn->reference_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                        @endif

                        @if($txn->transaction_type === 'payment' && Route::has('finance.operations.payment.print'))
                            <a target="_blank" href="{{ route('finance.operations.payment.print', $txn->reference_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                        @endif

                        @if($txn->transaction_type === 'loan' && Route::has('finance.operations.loan.print'))
                            <a target="_blank" href="{{ route('finance.operations.loan.print', $txn->reference_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                        @endif

                        @if($txn->transaction_type === 'transfer' && Route::has('finance.operations.transfer.print'))
                            <a target="_blank" href="{{ route('finance.operations.transfer.print', $txn->reference_no) }}" class="bg-slate-900 text-white px-3 py-2 text-[10px] font-bold">Print</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 font-bold">No transaction approvals waiting.</div>
            @endforelse
        </div>
    </div>
@endif

</div>