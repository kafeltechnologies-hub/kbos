<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

@php
    $projectNavLinks = [
        ['label' => 'Project Dashboard', 'route' => 'projects.dashboard'],
        ['label' => 'Project Centre', 'route' => 'projects.project-centre'],
        ['label' => 'Materials / Inventory', 'route' => 'projects.materials'],
        ['label' => 'Cost Entries', 'route' => 'projects.cost-entries'],
        ['label' => 'Project Payments', 'route' => 'projects.payments'],
        ['label' => 'Project Receipts', 'route' => 'projects.receipts'],
        ['label' => 'Project Quotations', 'route' => 'projects.quotations'],
        ['label' => 'Finance Dashboard', 'route' => 'finance.dashboard'],
    ];

    $inventoryTabs = [
        'dashboard' => 'Dashboard',
        'materials' => 'Material Master',
        'transactions' => 'Stock Transactions',
        'receipts' => 'Goods Receipts',
        'issues' => 'Material Issues',
        'waybills' => 'Waybills',
        'reports' => 'Reports',
    ];

    $reportTypes = [
        'stock_summary' => 'Stock Summary',
        'stock_valuation' => 'Stock Valuation',
        'low_stock' => 'Low Stock Items',
        'material_master' => 'Material Master List',
        'material_movement' => 'Material Movement',
        'material_ledger' => 'Material Ledger',
        'project_consumption' => 'Project Consumption',
        'goods_receipt_register' => 'Goods Receipt Register',
        'material_issue_register' => 'Material Issue Register',
        'waybill_register' => 'Waybill Register',
        'borrowed_stock_register' => 'Borrowed Stock Register',
        'project_transfer_register' => 'Project Transfer Register',
    ];

    $stockOutTypes = ['issue_project','issue_sale','issue_account','return_project','transfer_project','return_account'];
@endphp

{{-- TOP NAV --}}
<div class="border border-slate-300 bg-white shadow-sm overflow-hidden mb-6">
    <div class="bg-slate-950 px-4 py-3">
        <p class="text-[10px] font-bold uppercase tracking-widest text-green-300">Project Module</p>
        <h1 class="text-sm font-black text-white">ERP Materials / Inventory Control Centre</h1>
    </div>

    <div class="flex flex-wrap gap-2 p-3 bg-slate-900 border-t border-slate-800">
        @foreach($projectNavLinks as $link)
            @if(Route::has($link['route']))
                <a href="{{ route($link['route']) }}"
                   class="px-3 py-2 text-xs font-bold border {{ request()->routeIs($link['route']) ? 'bg-green-600 text-white border-green-500' : 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-700 hover:text-white' }}">
                    {{ $link['label'] }}
                </a>
            @endif
        @endforeach
    </div>

    <div class="flex flex-wrap gap-2 p-3 bg-slate-100 border-t border-slate-300">
        @foreach($inventoryTabs as $key => $label)
            <button type="button"
                    wire:click="$set('activeTab', '{{ $key }}')"
                    class="px-3 py-2 text-xs font-bold border {{ $activeTab === $key ? 'bg-green-700 text-white border-green-800' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>

{{-- FLASHES --}}
@if (session()->has('success'))
    <div class="mb-4 border-l-4 border-green-600 bg-green-50 p-3 text-xs font-medium text-green-900">{{ session('success') }}</div>
@endif

@if (session()->has('info'))
    <div class="mb-4 border-l-4 border-blue-600 bg-blue-50 p-3 text-xs font-medium text-blue-900">{{ session('info') }}</div>
@endif

@if ($errors->any())
    <div class="mb-4 border-l-4 border-red-600 bg-red-50 p-3 text-xs font-medium text-red-900">
        <div class="font-black mb-1">Please correct the highlighted fields:</div>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

{{-- SUMMARY CARDS --}}
<div class="w-full mb-6 overflow-x-auto">
    <div class="flex gap-4 min-w-max">
        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Total Materials</p>
            <p class="mt-2 text-2xl font-black font-mono">{{ $materials->count() }}</p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Transactions</p>
            <p class="mt-2 text-2xl font-black font-mono">{{ $transactions->count() }}</p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Pending Approval</p>
            <p class="mt-2 text-2xl font-black font-mono text-amber-700">
                {{ $transactions->filter(fn($t) => in_array(strtolower($t->status ?? ''), ['draft','posted','pending']))->count() }}
            </p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Approved</p>
            <p class="mt-2 text-2xl font-black font-mono text-green-700">{{ $transactions->where('status', 'approved')->count() }}</p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Waybills</p>
            <p class="mt-2 text-2xl font-black font-mono text-purple-700">{{ $waybillTransactions->filter(fn($t) => $t->waybill)->count() }}</p>
        </div>
    </div>
</div>

@if($activeTab === 'dashboard')

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-xs font-bold uppercase text-white">Low Stock / Reorder Watch</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Material</th>
                        <th class="px-4 py-3">Unit</th>
                        <th class="px-4 py-3 text-right">Stock</th>
                        <th class="px-4 py-3 text-right">Reorder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($materials as $material)
                        @php $stock = $stockBalances[$material->id] ?? 0; @endphp
                        @if($stock <= (float) ($material->reorder_level ?? 0))
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold text-amber-700">{{ $material->material_code }}</td>
                                <td class="px-4 py-3">{{ $material->name }}</td>
                                <td class="px-4 py-3">{{ $material->unit }}</td>
                                <td class="px-4 py-3 text-right font-mono">{{ number_format($stock, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono">{{ number_format((float) $material->reorder_level, 2) }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400 font-bold">No materials found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-xs font-bold uppercase text-white">Recent Stock Transactions</h2>
        </div>

        <div class="divide-y divide-slate-200">
            @forelse($transactions->take(8) as $transaction)
                <div class="p-4 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <p class="font-mono font-black text-green-700">{{ $transaction->transaction_no }}</p>
                        <p class="text-slate-500">{{ strtoupper(str_replace('_', ' ', $transaction->transaction_type)) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold">{{ $transaction->project?->project_name ?? $transaction->fromProject?->project_name ?? 'General Stock' }}</p>
                        <p class="text-slate-500">{{ strtoupper($transaction->status ?? '-') }}</p>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 font-bold text-xs">No transactions found.</div>
            @endforelse
        </div>
    </div>
</div>

@elseif($activeTab === 'materials')

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="space-y-6">

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
            <div class="bg-slate-800 px-4 py-3">
                <h2 class="text-sm font-bold text-white">Add / Define Material Category</h2>
            </div>

            <form wire:submit.prevent="saveCategory" class="p-5 space-y-4">
                <input type="text" wire:model="category_code" placeholder="Category Code e.g. CABLE, POLE, FITTING" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                <input type="text" wire:model="category_name" placeholder="Category Name e.g. Electrical Cables" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                <textarea wire:model="category_description" rows="2" placeholder="Category description" class="w-full text-xs border border-slate-300 px-2.5 py-2 resize-none"></textarea>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 text-xs font-bold bg-green-700 text-white border border-green-800">Save Category</button>
                    <button type="button" wire:click="clearCategoryForm" class="px-4 py-2 text-xs font-bold bg-white border border-slate-300">Clear Category</button>
                </div>
            </form>
        </div>

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
            <div class="bg-slate-800 px-4 py-3">
                <h2 class="text-sm font-bold text-white">{{ $isEditingMaterial ? 'Edit Material' : 'Add / Define Material' }}</h2>
            </div>

            <form wire:submit.prevent="saveMaterial" class="p-5 space-y-4">
                <input type="text" wire:model="material_code" placeholder="Material Code / Leave blank to auto-generate" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                <input type="text" wire:model="name" placeholder="Material Name" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                <textarea wire:model="description" rows="3" placeholder="Material specification, size, rating, type, brand, etc." class="w-full text-xs border border-slate-300 px-2.5 py-2 resize-none"></textarea>

                <div class="grid grid-cols-2 gap-3">
                    <select wire:model="category_id" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                    <input type="text" wire:model="unit" placeholder="Unit e.g. pcs, m, kg" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <input type="number" step="0.01" wire:model="standard_price" placeholder="Standard Cost" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                    <input type="number" step="0.01" wire:model="selling_price" placeholder="Selling Price" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <input type="number" step="0.01" wire:model="minimum_stock" placeholder="Min" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                    <input type="number" step="0.01" wire:model="maximum_stock" placeholder="Max" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                    <input type="number" step="0.01" wire:model="reorder_level" placeholder="Reorder" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                </div>

                <input type="text" wire:model="barcode" placeholder="Barcode / Serial" class="w-full text-xs border border-slate-300 px-2.5 py-2">

                <label class="flex items-center gap-2 text-xs font-bold text-slate-700">
                    <input type="checkbox" wire:model="active"> Active Material
                </label>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 text-xs font-bold bg-green-700 text-white border border-green-800">{{ $isEditingMaterial ? 'Update Material' : 'Save Material' }}</button>
                    <button type="button" wire:click="clearMaterialForm" class="px-4 py-2 text-xs font-bold bg-white border border-slate-300">Clear</button>
                </div>
            </form>
        </div>
    </div>

    <div class="xl:col-span-2 border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-xs font-bold uppercase text-white">Material Master List</h2>
        </div>

        <div class="p-3 bg-slate-100 border-b border-slate-300">
            <input wire:model.live.debounce.500ms="materialSearch" type="text" placeholder="Search material code, name, description..." class="w-full border border-slate-300 px-3 py-2 text-xs">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-4">Code</th>
                        <th class="px-4 py-4">Material</th>
                        <th class="px-4 py-4">Category</th>
                        <th class="px-4 py-4">Unit</th>
                        <th class="px-4 py-4 text-right">Cost</th>
                        <th class="px-4 py-4 text-right">Price</th>
                        <th class="px-4 py-4 text-right">Stock</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-4 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($materials as $material)
                        @php $stock = $stockBalances[$material->id] ?? 0; @endphp
                        <tr>
                            <td class="px-4 py-4 font-mono font-bold text-green-700">{{ $material->material_code }}</td>
                            <td class="px-4 py-4">
                                <div class="font-bold">{{ $material->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $material->description }}</div>
                            </td>
                            <td class="px-4 py-4">{{ $material->category?->category_name ?? '-' }}</td>
                            <td class="px-4 py-4">{{ $material->unit ?? '-' }}</td>
                            <td class="px-4 py-4 text-right font-mono">{{ number_format((float) $material->standard_price, 2) }}</td>
                            <td class="px-4 py-4 text-right font-mono">{{ number_format((float) $material->selling_price, 2) }}</td>
                            <td class="px-4 py-4 text-right font-mono">{{ number_format($stock, 2) }}</td>
                            <td class="px-4 py-4">{{ $material->active ? 'ACTIVE' : 'INACTIVE' }}</td>
                            <td class="px-4 py-4">
                                <button type="button" wire:click="editMaterial({{ $material->id }})" class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">Edit</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-10 text-center text-slate-400 font-bold">No materials found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@elseif($activeTab === 'transactions')

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- LEFT FORM --}}
    <div class="xl:col-span-1 border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-sm font-bold text-white">{{ $isEditingTransaction ? 'Edit Stock Transaction' : 'New Stock Transaction' }}</h2>
        </div>

        <form wire:submit.prevent="saveTransaction" class="p-5 space-y-4">

            <div>
                <label class="text-xs font-bold text-slate-600">Transaction Type</label>
                <select wire:model.live="transaction_type" class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2">
                    @foreach($transactionTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-600">Date</label>
                    <input type="date" wire:model="transaction_date" class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-600">Status</label>
                    <select wire:model="transaction_status" class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ strtoupper($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if(in_array($transaction_type, ['receive', 'purchase_resale']))
                <div class="border border-green-300 bg-green-50 p-4 space-y-3">
                    <h3 class="text-xs font-black uppercase text-green-800">Stock Receipt / Asset Increase</h3>
                    <p class="text-[10px] text-green-700">Finance Posting: Dr Inventory Asset, Cr Supplier Payable / Payment Voucher Clearing.</p>

                    @if($transaction_type === 'purchase_resale')
                        <label class="text-xs font-bold text-slate-600">Reference Finance Payment Voucher</label>
                        <select wire:model="payment_voucher_id" class="w-full text-xs border border-green-300 px-2.5 py-2">
                            <option value="">Select Payment Voucher</option>
                            @foreach($paymentVouchers as $voucher)
                                <option value="{{ $voucher->id }}">
                                    {{ $voucher->voucher_no ?? 'PV-'.$voucher->id }} —
                                    {{ $voucher->payee_name ?? $voucher->payee ?? 'Payee' }} —
                                    {{ number_format((float) ($voucher->gross_amount ?? $voucher->amount_paid ?? 0), 2) }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endif

            @if($transaction_type === 'issue_project')
                <div class="border border-blue-300 bg-blue-50 p-4 space-y-3">
                    <h3 class="text-xs font-black uppercase text-blue-800">Issue Stock To Project</h3>
                    <p class="text-[10px] text-blue-700">Finance Posting: Dr Project Material Cost, Cr Inventory Asset.</p>
                    <select wire:model="project_id" class="w-full text-xs border border-blue-300 px-2.5 py-2">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_code }} — {{ $project->project_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($transaction_type === 'return_project')
                <div class="border border-emerald-300 bg-emerald-50 p-4 space-y-3">
                    <h3 class="text-xs font-black uppercase text-emerald-800">Return Stock From Project</h3>
                    <p class="text-[10px] text-emerald-700">Finance Posting: Dr Inventory Asset, Cr Project Material Cost.</p>

                    <select wire:model.live="from_project_id" class="w-full text-xs border border-emerald-300 px-2.5 py-2">
                        <option value="">Select Project With Stock</option>
                        @foreach($issuedProjects ?? $projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_code }} — {{ $project->project_name }}</option>
                        @endforeach
                    </select>

                    @if(!empty($sourceProjectStock))
                        <div class="bg-white border border-emerald-200 p-3 max-h-56 overflow-y-auto">
                            <p class="text-[10px] font-black uppercase text-emerald-800 mb-2">Materials Held By Selected Project</p>
                            @foreach($sourceProjectStock as $stock)
                                <div class="flex justify-between text-[10px] border-b py-1">
                                    <span>{{ $stock['code'] }} — {{ $stock['name'] }}</span>
                                    <span class="font-mono font-bold">{{ number_format((float) $stock['balance'], 2) }} {{ $stock['unit'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if($transaction_type === 'transfer_project')
                <div class="border border-purple-300 bg-purple-50 p-4 space-y-3">
                    <h3 class="text-xs font-black uppercase text-purple-800">Transfer Stock Between Projects</h3>
                    <p class="text-[10px] text-purple-700">Finance Posting: Dr Destination Project Material Cost, Cr Source Project Material Cost.</p>

                    <select wire:model.live="from_project_id" class="w-full text-xs border border-purple-300 px-2.5 py-2">
                        <option value="">Select Source Project</option>
                        @foreach($issuedProjects ?? $projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_code }} — {{ $project->project_name }}</option>
                        @endforeach
                    </select>

                    @if(!empty($sourceProjectStock))
                        <div class="bg-white border border-purple-200 p-3 max-h-56 overflow-y-auto">
                            <p class="text-[10px] font-black uppercase text-purple-800 mb-2">Materials Available For Transfer</p>
                            @foreach($sourceProjectStock as $stock)
                                <div class="flex justify-between text-[10px] border-b py-1">
                                    <span>{{ $stock['code'] }} — {{ $stock['name'] }}</span>
                                    <span class="font-mono font-bold">{{ number_format((float) $stock['balance'], 2) }} {{ $stock['unit'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <select wire:model="to_project_id" class="w-full text-xs border border-purple-300 px-2.5 py-2">
                        <option value="">Select Destination Project</option>
                        @foreach($projects as $project)
                            @if((int) $project->id !== (int) $from_project_id)
                                <option value="{{ $project->id }}">{{ $project->project_code }} — {{ $project->project_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            @endif

            @if($transaction_type === 'issue_account')
                <div class="border border-amber-300 bg-amber-50 p-4 space-y-3">
                    <h3 class="text-xs font-black uppercase text-amber-800">Stock Issued On Account / Borrowed Out</h3>
                    <p class="text-[10px] text-amber-700">Finance Posting: Dr Material Receivables, Cr Inventory Asset.</p>
                    <input type="text" wire:model="account_holder_name" placeholder="Borrower / Account Holder Name" class="w-full text-xs border border-amber-300 px-2.5 py-2">
                    <input type="text" wire:model="account_holder_phone" placeholder="Phone Number" class="w-full text-xs border border-amber-300 px-2.5 py-2">
                    <input type="date" wire:model="expected_return_date" class="w-full text-xs border border-amber-300 px-2.5 py-2">
                </div>
            @endif

            @if($transaction_type === 'return_account')
                <div class="border border-teal-300 bg-teal-50 p-4 space-y-3">
                    <h3 class="text-xs font-black uppercase text-teal-800">Return Borrowed Stock</h3>
                    <p class="text-[10px] text-teal-700">Finance Posting: Dr Inventory Asset, Cr Material Receivables.</p>

                    <div class="bg-white border border-teal-200 max-h-60 overflow-y-auto">
                        @forelse($borrowedStock as $row)
                            <button type="button"
                                    wire:click="selectBorrowedStockForReturn('{{ addslashes($row['borrower_name']) }}', '{{ addslashes($row['borrower_phone'] ?? '') }}', {{ $row['material_id'] }}, {{ $row['balance'] }})"
                                    class="w-full text-left p-2 text-[10px] border-b hover:bg-teal-50">
                                <div class="font-bold">{{ $row['borrower_name'] }} — {{ $row['borrower_phone'] ?? '-' }}</div>
                                <div>{{ $row['material_code'] }} — {{ $row['material_name'] }}</div>
                                <div class="font-mono text-teal-700">Outstanding: {{ number_format((float) $row['balance'], 2) }} {{ $row['unit'] }}</div>
                            </button>
                        @empty
                            <div class="p-4 text-center text-slate-400 text-xs font-bold">No borrowed stock outstanding.</div>
                        @endforelse
                    </div>

                    <input type="text" wire:model="account_holder_name" placeholder="Borrower / Account Holder Name" class="w-full text-xs border border-teal-300 px-2.5 py-2">
                    <input type="text" wire:model="account_holder_phone" placeholder="Phone Number" class="w-full text-xs border border-teal-300 px-2.5 py-2">
                </div>
            @endif

            @if($transaction_type === 'issue_sale')
                <div class="border border-cyan-300 bg-cyan-50 p-4 space-y-3">
                    <h3 class="text-xs font-black uppercase text-cyan-800">Stock Issued For Sale</h3>
                    <p class="text-[10px] text-cyan-700">Finance Posting: Dr Cost of Goods Sold, Cr Inventory Asset. Also Dr Receipt Voucher Clearing, Cr Material Sales Revenue.</p>

                    <select wire:model="receipt_voucher_id" class="w-full text-xs border border-cyan-300 px-2.5 py-2">
                        <option value="">Select Receipt Voucher</option>
                        @foreach($receiptVouchers as $voucher)
                            <option value="{{ $voucher->id }}">
                                {{ $voucher->voucher_no ?? $voucher->receipt_no ?? 'RV-'.$voucher->id }} —
                                {{ $voucher->payee_name ?? $voucher->received_from ?? 'Customer' }} —
                                {{ number_format((float) ($voucher->amount_received ?? $voucher->amount ?? 0), 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <input type="text" wire:model="reference" placeholder="Transaction Reference" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <textarea wire:model="remarks" rows="3" placeholder="Remarks / Narration" class="w-full text-xs border border-slate-300 px-2.5 py-2 resize-none"></textarea>

            <div class="border border-slate-300">
                <div class="bg-slate-100 px-3 py-2 flex justify-between">
                    <span class="text-xs font-bold">Transaction Lines</span>
                    <button type="button" wire:click="addTransactionLine" class="text-[10px] font-bold text-green-700">+ Add Line</button>
                </div>

                <div class="space-y-3 p-3">
                    @foreach($transactionLines as $index => $line)
                        <div class="border border-slate-200 p-3 bg-slate-50">
                            <select wire:model.live="transactionLines.{{ $index }}.material_id"
                                    wire:change="materialSelected({{ $index }})"
                                    class="w-full text-xs border border-slate-300 px-2 py-1.5 mb-2">
                                <option value="">Select material</option>
                                @foreach($transactionMaterials as $material)
                                    @php
                                        $availableStock = $availableStocks[$material->id] ?? 0;
                                        $disableMaterial = in_array($transaction_type, $stockOutTypes, true) && $availableStock <= 0;
                                    @endphp
                                    <option value="{{ $material->id }}" @disabled($disableMaterial)>
                                        {{ $material->material_code }} — {{ $material->name }}
                                        | Available: {{ number_format((float) $availableStock, 2) }}
                                        @if($disableMaterial) — OUT OF STOCK @endif
                                    </option>
                                @endforeach
                            </select>

                            <div class="grid grid-cols-3 gap-2">
                                <input type="number" step="0.01" wire:model.live="transactionLines.{{ $index }}.quantity" placeholder="Qty" class="text-xs border border-slate-300 px-2 py-1.5 w-full">
                                <input type="number" step="0.01" wire:model.live="transactionLines.{{ $index }}.unit_cost" placeholder="Unit Cost" class="text-xs border border-slate-300 px-2 py-1.5 w-full">
                                <input type="text" readonly value="{{ number_format((float) ($line['line_total'] ?? 0), 2) }}" class="text-xs border border-slate-300 px-2 py-1.5 bg-slate-200 font-mono w-full">
                            </div>

                            <div class="mt-2 text-[10px] text-slate-500">{{ $line['description'] ?? '' }}</div>

                            <button type="button" wire:click="removeTransactionLine({{ $index }})" class="mt-2 text-[10px] font-bold text-red-700">Remove Line</button>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($transaction_type === 'issue_project')
                <div class="border border-purple-300 bg-purple-50 p-4 space-y-3">
                    <h3 class="text-xs font-bold text-purple-800 uppercase">Optional Transport Waybill</h3>
                    <input type="text" wire:model="transporter_name" placeholder="Transporter Name" class="w-full text-xs border border-purple-300 px-2 py-1.5">
                    <input type="text" wire:model="driver_name" placeholder="Driver Name" class="w-full text-xs border border-purple-300 px-2 py-1.5">
                    <input type="text" wire:model="driver_phone" placeholder="Driver Phone" class="w-full text-xs border border-purple-300 px-2 py-1.5">
                    <input type="text" wire:model="vehicle_number" placeholder="Vehicle Number" class="w-full text-xs border border-purple-300 px-2 py-1.5">
                    <input type="text" wire:model="delivery_location" placeholder="Delivery Location" class="w-full text-xs border border-purple-300 px-2 py-1.5">
                    <input type="text" wire:model="loaded_by" placeholder="Loaded By" class="w-full text-xs border border-purple-300 px-2 py-1.5">
                    <input type="text" wire:model="received_by" placeholder="Received By" class="w-full text-xs border border-purple-300 px-2 py-1.5">
                </div>
            @endif

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-green-700 text-white border border-green-800">{{ $isEditingTransaction ? 'Update Transaction' : 'Save Transaction' }}</button>
                <button type="button" wire:click="clearTransactionForm" class="px-4 py-2 text-xs font-bold bg-white border border-slate-300">Clear Transaction</button>
            </div>
        </form>
    </div>

    {{-- RIGHT REGISTER --}}
    <div class="xl:col-span-2 space-y-4">
        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
            <div class="bg-slate-900 px-4 py-3 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-green-300">Inventory Control</p>
                    <h2 class="text-sm font-black text-white">Stock Transaction Register</h2>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-slate-400 uppercase font-bold">Records</p>
                    <p class="text-white font-mono font-black">{{ $transactions->count() }}</p>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-b border-slate-300">
                <input wire:model.live.debounce.500ms="transactionSearch"
                       type="text"
                       placeholder="Search transaction no, type, project, borrower, reference or status..."
                       class="w-full border border-slate-300 px-3 py-2 text-xs bg-white">
            </div>
        </div>

        @forelse($transactions as $transaction)
            @php
                $status = strtolower($transaction->status ?? '');
                $type = $transaction->transaction_type;
                $transactionValue = (float) ($transaction->lines?->sum('line_total') ?? 0);
                $lineCount = $transaction->lines?->count() ?? 0;

                $statusClass = match($status) {
                    'approved' => 'bg-green-50 text-green-700 border-green-300',
                    'draft', 'posted', 'pending' => 'bg-amber-50 text-amber-700 border-amber-300',
                    'reversed' => 'bg-red-50 text-red-700 border-red-300',
                    'cancelled' => 'bg-slate-100 text-slate-600 border-slate-300',
                    default => 'bg-slate-100 text-slate-700 border-slate-300',
                };

                $typeClass = match($type) {
                    'receive', 'purchase_resale', 'return_project', 'return_account' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
                    'issue_project', 'issue_sale', 'issue_account' => 'bg-blue-50 text-blue-700 border-blue-300',
                    'transfer_project' => 'bg-purple-50 text-purple-700 border-purple-300',
                    default => 'bg-slate-50 text-slate-700 border-slate-300',
                };
            @endphp

            <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200">
                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-sm font-black text-green-700">{{ $transaction->transaction_no }}</span>
                            <span class="px-2 py-1 text-[10px] font-black uppercase border {{ $typeClass }}">{{ str_replace('_', ' ', $type) }}</span>
                            <span class="px-2 py-1 text-[10px] font-black uppercase border {{ $statusClass }}">{{ $transaction->status ?? '-' }}</span>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @if(in_array($status, ['draft', 'posted', 'pending']))
                                <button wire:click="editTransaction({{ $transaction->id }})" class="px-3 py-1.5 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">Edit</button>
                                <button wire:click="approveTransaction({{ $transaction->id }})" class="px-3 py-1.5 text-[10px] font-bold bg-green-600 text-white border border-green-700">Approve</button>
                                <button wire:click="deleteTransaction({{ $transaction->id }})" class="px-3 py-1.5 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">Delete</button>
                            @endif

                            @if($status === 'approved')
                                <button wire:click="reverseTransaction({{ $transaction->id }})" class="px-3 py-1.5 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">Reverse</button>
                            @endif

                            @if(in_array($type, ['receive', 'purchase_resale', 'return_project', 'return_account']) && Route::has('projects.materials.receipt.print'))
                                <a href="{{ route('projects.materials.receipt.print', $transaction) }}" target="_blank" class="px-3 py-1.5 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-300">Receipt</a>
                            @endif

                            @if(in_array($type, ['issue_project', 'issue_sale', 'issue_account', 'transfer_project']) && Route::has('projects.materials.issue.print'))
                                <a href="{{ route('projects.materials.issue.print', $transaction) }}" target="_blank" class="px-3 py-1.5 text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-300">Issue</a>
                            @endif

                            @if($transaction->waybill && Route::has('projects.materials.waybill.print'))
                                <a href="{{ route('projects.materials.waybill.print', $transaction->waybill) }}" target="_blank" class="px-3 py-1.5 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">Waybill</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="bg-white border border-slate-200 p-3">
                            <p class="text-[10px] font-bold uppercase text-slate-500">Date</p>
                            <p class="mt-1 text-xs font-bold">{{ $transaction->transaction_date?->format('d M Y') ?? '-' }}</p>
                        </div>

                        <div class="bg-white border border-slate-200 p-3">
                            <p class="text-[10px] font-bold uppercase text-slate-500">Movement</p>
                            <p class="mt-1 text-xs font-bold">
                                @if($type === 'transfer_project')
                                    {{ $transaction->fromProject?->project_name ?? '-' }} → {{ $transaction->toProject?->project_name ?? '-' }}
                                @elseif($type === 'issue_account')
                                    Borrower: {{ $transaction->account_holder_name ?? '-' }}
                                @elseif($type === 'return_account')
                                    Returned By: {{ $transaction->account_holder_name ?? '-' }}
                                @else
                                    {{ $transaction->project?->project_name ?? 'General Stock' }}
                                @endif
                            </p>
                        </div>

                        <div class="bg-white border border-slate-200 p-3">
                            <p class="text-[10px] font-bold uppercase text-slate-500">Finance Ref</p>
                            <p class="mt-1 text-xs font-bold">
                                @if($type === 'purchase_resale')
                                    PV: {{ $transaction->paymentVoucher?->voucher_no ?? $transaction->payment_voucher_id ?? '-' }}
                                @elseif($type === 'issue_sale')
                                    RV: {{ $transaction->receiptVoucher?->voucher_no ?? $transaction->receipt_voucher_id ?? '-' }}
                                @else
                                    {{ $transaction->reference ?? '-' }}
                                @endif
                            </p>
                        </div>

                        <div class="bg-white border border-slate-200 p-3">
                            <p class="text-[10px] font-bold uppercase text-slate-500">Items / Value</p>
                            <p class="mt-1 text-xs"><span class="font-black">{{ $lineCount }}</span> item(s) | <span class="font-mono font-black">{{ number_format($transactionValue, 2) }}</span></p>
                        </div>
                    </div>

                    @if($transaction->remarks)
                        <div class="mt-3 bg-white border border-slate-200 p-3">
                            <p class="text-[10px] font-bold uppercase text-slate-500">Remarks</p>
                            <p class="mt-1 text-xs text-slate-700">{{ $transaction->remarks }}</p>
                        </div>
                    @endif
                </div>

                <div class="border-t border-slate-200">
                    <div class="px-4 py-2 bg-slate-100 flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase text-slate-600">Material Lines</p>
                        <p class="text-[10px] font-bold text-slate-500">{{ $lineCount }} line(s)</p>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($transaction->lines as $line)
                            <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
                                <div class="md:col-span-2">
                                    <p class="font-black text-slate-800">{{ $line->material?->material_code }} — {{ $line->material?->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $line->material?->description }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-slate-500">Quantity / Unit Cost</p>
                                    <p class="font-mono">{{ number_format((float) $line->quantity, 2) }} × {{ number_format((float) $line->unit_cost, 2) }}</p>
                                </div>
                                <div class="md:text-right">
                                    <p class="text-[10px] font-bold uppercase text-slate-500">Amount</p>
                                    <p class="font-mono font-black">{{ number_format((float) $line->line_total, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-xs text-slate-400 font-bold">No material lines found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-300 p-10 text-center text-slate-400 font-bold">No stock transactions found.</div>
        @endforelse
    </div>
</div>

@elseif($activeTab === 'receipts')

<div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
    <div class="bg-slate-800 px-4 py-3">
        <h2 class="text-xs font-bold uppercase text-white">Goods Receipt / Return Register</h2>
    </div>

    <div class="p-3 bg-slate-100 border-b border-slate-300">
        <input wire:model.live.debounce.500ms="receiptSearch" type="text" placeholder="Search receipt number, reference, status..." class="w-full border border-slate-300 px-3 py-2 text-xs">
    </div>

    <div class="divide-y divide-slate-200">
        @forelse($receiptTransactions as $transaction)
            <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-xs">
                <div>
                    <p class="font-mono font-black text-green-700">{{ $transaction->transaction_no }}</p>
                    <p>{{ strtoupper(str_replace('_', ' ', $transaction->transaction_type)) }} — {{ $transaction->transaction_date?->format('d M Y') }}</p>
                    <p class="text-slate-500">{{ $transaction->reference ?? '-' }}</p>
                </div>

                <div class="flex flex-wrap gap-2 items-center">
                    <span class="font-mono font-black">{{ number_format((float) ($transaction->lines?->sum('line_total') ?? 0), 2) }}</span>
                    <span class="px-2 py-1 text-[10px] font-bold border">{{ strtoupper($transaction->status ?? '-') }}</span>

                    @if(Route::has('projects.materials.receipt.print'))
                        <a href="{{ route('projects.materials.receipt.print', $transaction) }}" target="_blank" class="px-2 py-1 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-300">Print Receipt</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-4 py-10 text-center text-slate-400 font-bold text-xs">No goods receipts found.</div>
        @endforelse
    </div>
</div>

@elseif($activeTab === 'issues')

<div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
    <div class="bg-slate-800 px-4 py-3">
        <h2 class="text-xs font-bold uppercase text-white">Material Issue / Transfer / Borrow Register</h2>
    </div>

    <div class="p-3 bg-slate-100 border-b border-slate-300">
        <input wire:model.live.debounce.500ms="issueSearch" type="text" placeholder="Search issue number, reference, borrower, status..." class="w-full border border-slate-300 px-3 py-2 text-xs">
    </div>

    <div class="divide-y divide-slate-200">
        @forelse($issueTransactions as $transaction)
            <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-xs">
                <div>
                    <p class="font-mono font-black text-blue-700">{{ $transaction->transaction_no }}</p>
                    <p>{{ strtoupper(str_replace('_', ' ', $transaction->transaction_type)) }} — {{ $transaction->transaction_date?->format('d M Y') }}</p>
                    <p class="text-slate-500">
                        @if($transaction->transaction_type === 'transfer_project')
                            {{ $transaction->fromProject?->project_name ?? '-' }} → {{ $transaction->toProject?->project_name ?? '-' }}
                        @elseif($transaction->transaction_type === 'issue_account')
                            Borrower: {{ $transaction->account_holder_name ?? '-' }}
                        @else
                            {{ $transaction->project?->project_name ?? '-' }}
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 items-center">
                    <span class="font-mono font-black">{{ number_format((float) ($transaction->lines?->sum('line_total') ?? 0), 2) }}</span>
                    <span class="px-2 py-1 text-[10px] font-bold border">{{ strtoupper($transaction->status ?? '-') }}</span>

                    @if(Route::has('projects.materials.issue.print'))
                        <a href="{{ route('projects.materials.issue.print', $transaction) }}" target="_blank" class="px-2 py-1 text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-300">Print Issue</a>
                    @endif

                    @if($transaction->waybill && Route::has('projects.materials.waybill.print'))
                        <a href="{{ route('projects.materials.waybill.print', $transaction->waybill) }}" target="_blank" class="px-2 py-1 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">Print Waybill</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-4 py-10 text-center text-slate-400 font-bold text-xs">No material issues found.</div>
        @endforelse
    </div>
</div>

@elseif($activeTab === 'waybills')

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-sm font-bold text-white">{{ $editingWaybillId ? 'Edit Waybill' : 'Create Waybill' }}</h2>
        </div>

        <form wire:submit.prevent="saveWaybill" class="p-5 space-y-4">
            <select wire:model="waybill_transaction_id" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                <option value="">Select issue transaction</option>
                @foreach($waybillTransactions as $transaction)
                    <option value="{{ $transaction->id }}">{{ $transaction->transaction_no }} — {{ $transaction->project?->project_name ?? 'No Project' }}</option>
                @endforeach
            </select>

            <input type="text" wire:model="transporter_name" placeholder="Transporter Name" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="driver_name" placeholder="Driver Name" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="driver_phone" placeholder="Driver Phone" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="vehicle_number" placeholder="Vehicle Number" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="delivery_location" placeholder="Delivery Location" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="loaded_by" placeholder="Loaded By" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="received_by" placeholder="Received By" class="w-full text-xs border border-slate-300 px-2.5 py-2">

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-green-700 text-white border border-green-800">Save Waybill</button>
                <button type="button" wire:click="clearWaybillForm" class="px-4 py-2 text-xs font-bold bg-white border border-slate-300">Clear</button>
            </div>
        </form>
    </div>

    <div class="xl:col-span-2 border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-xs font-bold uppercase text-white">Waybill Register</h2>
        </div>

        <div class="p-3 bg-slate-100 border-b border-slate-300">
            <input wire:model.live.debounce.500ms="waybillSearch" type="text" placeholder="Search waybill no, transporter, driver, vehicle, project..." class="w-full border border-slate-300 px-3 py-2 text-xs">
        </div>

        <div class="divide-y divide-slate-200">
            @forelse($waybillTransactions as $transaction)
                <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-xs">
                    <div>
                        <p class="font-mono font-black text-amber-700">{{ $transaction->waybill?->waybill_no ?? 'NOT CREATED' }}</p>
                        <p>{{ $transaction->transaction_no }} — {{ $transaction->project?->project_name ?? '-' }}</p>
                        <p class="text-slate-500">
                            {{ $transaction->waybill?->transporter_name ?? '-' }} |
                            {{ $transaction->waybill?->driver_name ?? '-' }} |
                            {{ $transaction->waybill?->vehicle_number ?? '-' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if(! $transaction->waybill)
                            <button type="button" wire:click="createWaybill({{ $transaction->id }})" class="px-2 py-1 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">Create</button>
                        @else
                            <button type="button" wire:click="editWaybill({{ $transaction->waybill->id }})" class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">Edit</button>
                            @if(Route::has('projects.materials.waybill.print'))
                                <a href="{{ route('projects.materials.waybill.print', $transaction->waybill) }}" target="_blank" class="px-2 py-1 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">Print</a>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-10 text-center text-slate-400 font-bold text-xs">No project issue transactions found.</div>
            @endforelse
        </div>
    </div>
</div>

@elseif($activeTab === 'reports')

<div class="space-y-6">
    <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-900 px-5 py-4">
            <p class="text-[10px] text-green-300 font-bold uppercase tracking-widest">Inventory Reporting Centre</p>
            <h2 class="text-white text-sm font-black">Generate Specific Inventory Reports</h2>
        </div>

        <form method="GET" action="{{ Route::has('projects.materials.reports.print') ? route('projects.materials.reports.print') : '#' }}" target="_blank" class="p-5 bg-slate-50 border-b border-slate-300">
            <div class="grid grid-cols-1 md:grid-cols-8 gap-3 items-end">
                <div>
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Report Type</label>
                    <select name="type" id="report_type" onchange="toggleReportFilters()" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                        @foreach($reportTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="project_filter_box">
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Project</label>
                    <select name="project_id" id="project_id" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_code }} — {{ $project->project_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="material_filter_box">
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Material</label>
                    <select name="material_id" id="material_id" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                        <option value="">All Materials</option>
                        @foreach($allMaterials as $material)
                            <option value="{{ $material->id }}">{{ $material->material_code }} — {{ $material->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="status_filter_box">
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Status</label>
                    <select name="status" id="status" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                        <option value="">All</option>
                        <option value="draft">Draft</option>
                        <option value="approved">Approved</option>
                        <option value="reversed">Reversed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Date From</label>
                    <input type="date" name="date_from" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Date To</label>
                    <input type="date" name="date_to" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Search</label>
                    <input type="text" name="search" placeholder="Keyword" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Action</label>
                    <select name="print" class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                        <option value="">Preview</option>
                        <option value="1">Print</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                @if(Route::has('projects.materials.reports.print'))
                    <button type="submit" class="px-5 py-2 text-xs font-bold bg-green-700 text-white border border-green-800 hover:bg-green-800">Generate Report</button>
                    <button type="reset" class="px-5 py-2 text-xs font-bold bg-white text-slate-700 border border-slate-300 hover:bg-slate-100">Clear Filters</button>
                @else
                    <div class="px-4 py-2 text-xs font-bold bg-red-50 text-red-700 border border-red-300">Missing route: projects.materials.reports.print</div>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
    function toggleReportFilters() {
        const type = document.getElementById('report_type')?.value;
        const projectBox = document.getElementById('project_filter_box');
        const materialBox = document.getElementById('material_filter_box');
        const statusBox = document.getElementById('status_filter_box');

        const projectReports = ['project_consumption', 'material_issue_register', 'waybill_register', 'project_transfer_register'];
        const materialReports = ['stock_summary', 'stock_valuation', 'low_stock', 'material_master', 'material_movement', 'material_ledger', 'borrowed_stock_register'];
        const statusReports = ['material_movement', 'material_ledger', 'goods_receipt_register', 'material_issue_register', 'borrowed_stock_register', 'project_transfer_register'];

        projectBox.style.display = projectReports.includes(type) ? 'block' : 'none';
        materialBox.style.display = materialReports.includes(type) ? 'block' : 'none';
        statusBox.style.display = statusReports.includes(type) ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', toggleReportFilters);
    document.addEventListener('livewire:navigated', toggleReportFilters);
</script>

@endif

</div>
