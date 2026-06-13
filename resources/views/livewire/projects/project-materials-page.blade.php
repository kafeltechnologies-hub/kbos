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
    ];
@endphp

<div class="border border-slate-300 bg-white shadow-sm overflow-hidden mb-6">
    <div class="bg-slate-950 px-4 py-3">
        <p class="text-[10px] font-bold uppercase tracking-widest text-green-300">Project Module</p>
        <h1 class="text-sm font-black text-white">ERP Materials / Inventory Control Centre</h1>
    </div>

    <div class="flex flex-wrap gap-2 p-3 bg-slate-900 border-t border-slate-800">
        @foreach($projectNavLinks as $link)
            @if(Route::has($link['route']))
                <a href="{{ route($link['route']) }}"
                   class="px-3 py-2 text-xs font-bold border
                   {{ request()->routeIs($link['route'])
                        ? 'bg-green-600 text-white border-green-500'
                        : 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-700 hover:text-white' }}">
                    {{ $link['label'] }}
                </a>
            @endif
        @endforeach
    </div>

    <div class="flex flex-wrap gap-2 p-3 bg-slate-100 border-t border-slate-300">
        @foreach($inventoryTabs as $key => $label)
            <button type="button"
                    wire:click="$set('activeTab', '{{ $key }}')"
                    class="px-3 py-2 text-xs font-bold border
                    {{ $activeTab === $key
                        ? 'bg-green-700 text-white border-green-800'
                        : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>

@if (session()->has('success'))
    <div class="mb-4 border-l-4 border-green-600 bg-green-50 p-3 text-xs font-medium text-green-900">
        {{ session('success') }}
    </div>
@endif

@if (session()->has('info'))
    <div class="mb-4 border-l-4 border-blue-600 bg-blue-50 p-3 text-xs font-medium text-blue-900">
        {{ session('info') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 border-l-4 border-red-600 bg-red-50 p-3 text-xs font-medium text-red-900">
        Please correct the highlighted fields.
    </div>
@endif

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
                {{ $transactions->where('status', 'draft')->count() }}
            </p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Approved</p>
            <p class="mt-2 text-2xl font-black font-mono text-green-700">
                {{ $transactions->where('status', 'approved')->count() }}
            </p>
        </div>

        <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Waybills</p>
            <p class="mt-2 text-2xl font-black font-mono text-purple-700">
                {{ $waybillTransactions->filter(fn($t) => $t->waybill)->count() }}
            </p>
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
                        @php $stock = $this->stockQuantity($material->id); @endphp

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
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-bold">
                                No materials found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-xs font-bold uppercase text-white">Recent Stock Transactions</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Project</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse($transactions->take(10) as $transaction)
                        <tr>
                            <td class="px-4 py-3 font-mono font-bold">{{ $transaction->transaction_no }}</td>
                            <td class="px-4 py-3">{{ strtoupper(str_replace('_', ' ', $transaction->transaction_type)) }}</td>
                            <td class="px-4 py-3">{{ $transaction->project?->project_name ?? 'General Stock' }}</td>
                            <td class="px-4 py-3">{{ strtoupper($transaction->status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-bold">
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                <div>
                    <label class="text-xs font-bold text-slate-600">Category Code</label>
                    <input type="text" wire:model="category_code"
                           placeholder="Example: CABLE, POLE, FITTING"
                           class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2">
                    <p class="mt-1 text-[10px] text-slate-500">Short code used to group materials.</p>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-600">Category Name</label>
                    <input type="text" wire:model="category_name"
                           placeholder="Example: Electrical Cables"
                           class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-600">Category Description</label>
                    <textarea wire:model="category_description" rows="2"
                              placeholder="Describe what kind of materials belong to this category"
                              class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2 resize-none"></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 text-xs font-bold bg-green-700 text-white border border-green-800">
                        Save Category
                    </button>

                    <button type="button" wire:click="clearCategoryForm"
                            class="px-4 py-2 text-xs font-bold bg-white border border-slate-300">
                        Clear Category
                    </button>
                </div>
            </form>
        </div>

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
            <div class="bg-slate-800 px-4 py-3">
                <h2 class="text-sm font-bold text-white">
                    {{ $isEditingMaterial ? 'Edit Material' : 'Add / Define Material' }}
                </h2>
            </div>

            <form wire:submit.prevent="saveMaterial" class="p-5 space-y-4">
                <div>
                    <label class="text-xs font-bold text-slate-600">Material Code</label>
                    <input type="text" wire:model="material_code"
                           placeholder="Leave blank to auto-generate"
                           class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-600">Material Name</label>
                    <input type="text" wire:model="name"
                           placeholder="Example: ABC Aluminium Conductor"
                           class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2">
                    @error('name') <p class="text-[11px] text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-600">Material Description</label>
                    <textarea wire:model="description" rows="3"
                              placeholder="Describe specification, size, rating, type, brand, etc."
                              class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2 resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-600">Category</label>
                        <select wire:model="category_id" class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2">
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-600">Unit</label>
                        <input type="text" wire:model="unit"
                               placeholder="pcs, m, kg, roll, bag"
                               class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <input type="number" step="0.01" wire:model="standard_price"
                           placeholder="Standard Cost"
                           class="w-full text-xs border border-slate-300 px-2.5 py-2">

                    <input type="number" step="0.01" wire:model="selling_price"
                           placeholder="Selling Price"
                           class="w-full text-xs border border-slate-300 px-2.5 py-2">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <input type="number" step="0.01" wire:model="minimum_stock"
                           placeholder="Min Stock"
                           class="w-full text-xs border border-slate-300 px-2.5 py-2">

                    <input type="number" step="0.01" wire:model="maximum_stock"
                           placeholder="Max Stock"
                           class="w-full text-xs border border-slate-300 px-2.5 py-2">

                    <input type="number" step="0.01" wire:model="reorder_level"
                           placeholder="Reorder"
                           class="w-full text-xs border border-slate-300 px-2.5 py-2">
                </div>

                <input type="text" wire:model="barcode"
                       placeholder="Barcode / Serial"
                       class="w-full text-xs border border-slate-300 px-2.5 py-2">

                <label class="flex items-center gap-2 text-xs font-bold text-slate-700">
                    <input type="checkbox" wire:model="active">
                    Active Material
                </label>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 text-xs font-bold bg-green-700 text-white border border-green-800">
                        {{ $isEditingMaterial ? 'Update Material' : 'Save Material' }}
                    </button>

                    <button type="button" wire:click="clearMaterialForm"
                            class="px-4 py-2 text-xs font-bold bg-white border border-slate-300">
                        Clear Material Form
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="xl:col-span-2 border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-xs font-bold uppercase text-white">Material Master List</h2>
        </div>

        <div class="p-3 bg-slate-100 border-b border-slate-300">
            <input wire:model.live.debounce.500ms="materialSearch"
                   type="text"
                   placeholder="Search material code, name, description..."
                   class="w-full border border-slate-300 px-3 py-2 text-xs">
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
                        @php $stock = $this->stockQuantity($material->id); @endphp
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
                                <button type="button" wire:click="editMaterial({{ $material->id }})"
                                        class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-slate-400 font-bold">
                                No materials found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@elseif($activeTab === 'transactions')

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-1 border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-sm font-bold text-white">
                {{ $isEditingTransaction ? 'Edit Stock Transaction' : 'New Stock Transaction' }}
            </h2>
        </div>

        <form wire:submit.prevent="saveTransaction" class="p-5 space-y-4">
            <select wire:model="transaction_type" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                @foreach($transactionTypes as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>

            <input type="date" wire:model="transaction_date"
                   class="w-full text-xs border border-slate-300 px-2.5 py-2">

            <select wire:model="project_id" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                <option value="">General Stock / No Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">
                        {{ $project->project_code }} — {{ $project->project_name }}
                    </option>
                @endforeach
            </select>

            <input type="text" wire:model="reference" placeholder="Reference"
                   class="w-full text-xs border border-slate-300 px-2.5 py-2">

            <select wire:model="transaction_status" class="w-full text-xs border border-slate-300 px-2.5 py-2">
                @foreach($statuses as $status)
                    <option value="{{ $status }}">{{ strtoupper($status) }}</option>
                @endforeach
            </select>

            <textarea wire:model="remarks" rows="3" placeholder="Remarks"
                      class="w-full text-xs border border-slate-300 px-2.5 py-2 resize-none"></textarea>

            <div class="border border-slate-300">
                <div class="bg-slate-100 px-3 py-2 flex justify-between">
                    <span class="text-xs font-bold">Transaction Lines</span>
                    <button type="button" wire:click="addTransactionLine"
                            class="text-[10px] font-bold text-green-700">
                        + Add Line
                    </button>
                </div>

                <div class="space-y-3 p-3">
                    @foreach($transactionLines as $index => $line)
                        <div class="border border-slate-200 p-3 bg-slate-50">
                            <select wire:model.live="transactionLines.{{ $index }}.material_id"
                                    wire:change="materialSelected({{ $index }})"
                                    class="w-full text-xs border border-slate-300 px-2 py-1.5 mb-2">
                                <option value="">Select material</option>
                                @foreach($allMaterials as $material)
                                    <option value="{{ $material->id }}">
                                        {{ $material->material_code }} — {{ $material->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="grid grid-cols-3 gap-2">
                                <input type="number" step="0.01"
                                       wire:model.live="transactionLines.{{ $index }}.quantity"
                                       placeholder="Qty"
                                       class="text-xs border border-slate-300 px-2 py-1.5">

                                <input type="number" step="0.01"
                                       wire:model.live="transactionLines.{{ $index }}.unit_cost"
                                       placeholder="Unit Cost"
                                       class="text-xs border border-slate-300 px-2 py-1.5">

                                <input type="text" readonly
                                       value="{{ number_format((float) ($line['line_total'] ?? 0), 2) }}"
                                       class="text-xs border border-slate-300 px-2 py-1.5 bg-slate-200 font-mono">
                            </div>

                            <button type="button" wire:click="removeTransactionLine({{ $index }})"
                                    class="mt-2 text-[10px] font-bold text-red-700">
                                Remove Line
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-green-700 text-white border border-green-800">
                    {{ $isEditingTransaction ? 'Update Transaction' : 'Save Transaction' }}
                </button>

                <button type="button" wire:click="clearTransactionForm"
                        class="px-4 py-2 text-xs font-bold bg-white border border-slate-300">
                    Clear Transaction
                </button>
            </div>
        </form>
    </div>

    <div class="xl:col-span-2 border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-xs font-bold uppercase text-white">Stock Transaction Register</h2>
        </div>

        <div class="p-3 bg-slate-100 border-b border-slate-300">
            <input wire:model.live.debounce.500ms="transactionSearch"
                   type="text"
                   placeholder="Search transaction no, reference, type, status..."
                   class="w-full border border-slate-300 px-3 py-2 text-xs">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-4">No.</th>
                        <th class="px-4 py-4">Date</th>
                        <th class="px-4 py-4">Type</th>
                        <th class="px-4 py-4">Project</th>
                        <th class="px-4 py-4">Items</th>
                        <th class="px-4 py-4">Value</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-4 py-4">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-4 py-4 font-mono font-bold">{{ $transaction->transaction_no }}</td>
                            <td class="px-4 py-4">{{ $transaction->transaction_date?->format('Y-m-d') }}</td>
                            <td class="px-4 py-4">{{ strtoupper(str_replace('_', ' ', $transaction->transaction_type)) }}</td>
                            <td class="px-4 py-4">{{ $transaction->project?->project_name ?? 'General Stock' }}</td>
                            <td class="px-4 py-4">{{ $transaction->lines->count() }}</td>
                            <td class="px-4 py-4 font-mono">{{ number_format((float) $transaction->lines->sum('line_total'), 2) }}</td>
                            <td class="px-4 py-4">{{ strtoupper($transaction->status) }}</td>
                            <td class="px-4 py-4">
                                @if($transaction->status === 'draft')
                                    <button wire:click="editTransaction({{ $transaction->id }})" class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">Edit</button>
                                    <button wire:click="approveTransaction({{ $transaction->id }})" class="px-2 py-1 text-[10px] font-bold bg-green-50 text-green-700 border border-green-300">Approve</button>
                                    <button wire:click="deleteTransaction({{ $transaction->id }})" class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">Delete</button>
                                @endif

                                @if($transaction->status === 'approved')
                                    <button wire:click="reverseTransaction({{ $transaction->id }})" class="px-2 py-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-300">Reverse</button>
                                @endif

                                @if($transaction->transaction_type === 'receive' && Route::has('projects.materials.receipt.print'))
                                    <a href="{{ route('projects.materials.receipt.print', $transaction) }}" target="_blank" class="px-2 py-1 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-300">GRN</a>
                                @endif

                                @if(in_array($transaction->transaction_type, ['issue_project', 'issue_sale']) && Route::has('projects.materials.issue.print'))
                                    <a href="{{ route('projects.materials.issue.print', $transaction) }}" target="_blank" class="px-2 py-1 text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-300">Issue</a>
                                @endif

                                @if($transaction->transaction_type === 'issue_project' && ! $transaction->waybill)
                                    <button type="button" wire:click="createWaybill({{ $transaction->id }})" class="px-2 py-1 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">Create Waybill</button>
                                @endif

                                @if($transaction->waybill && Route::has('projects.materials.waybill.print'))
                                    <a href="{{ route('projects.materials.waybill.print', $transaction->waybill) }}" target="_blank" class="px-2 py-1 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">Waybill</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-400 font-bold">
                                No stock transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@elseif($activeTab === 'receipts')

<div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
    <div class="bg-slate-800 px-4 py-3">
        <h2 class="text-xs font-bold uppercase text-white">Goods Receipt Register</h2>
    </div>

    <div class="p-3 bg-slate-100 border-b border-slate-300">
        <input wire:model.live.debounce.500ms="receiptSearch"
               type="text"
               placeholder="Search GRN number, reference, status..."
               class="w-full border border-slate-300 px-3 py-2 text-xs">
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left whitespace-nowrap">
            <thead class="bg-slate-100 text-slate-700">
                <tr>
                    <th class="px-4 py-4">GRN No.</th>
                    <th class="px-4 py-4">Date</th>
                    <th class="px-4 py-4">Reference</th>
                    <th class="px-4 py-4">Items</th>
                    <th class="px-4 py-4">Value</th>
                    <th class="px-4 py-4">Status</th>
                    <th class="px-4 py-4">Print</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse($receiptTransactions as $transaction)
                    <tr>
                        <td class="px-4 py-4 font-mono font-bold">{{ $transaction->transaction_no }}</td>
                        <td class="px-4 py-4">{{ $transaction->transaction_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-4">{{ $transaction->reference ?? '-' }}</td>
                        <td class="px-4 py-4">{{ $transaction->lines->count() }}</td>
                        <td class="px-4 py-4 font-mono">{{ number_format((float) $transaction->lines->sum('line_total'), 2) }}</td>
                        <td class="px-4 py-4">{{ strtoupper($transaction->status) }}</td>
                        <td class="px-4 py-4">
                            @if(Route::has('projects.materials.receipt.print'))
                                <a href="{{ route('projects.materials.receipt.print', $transaction) }}" target="_blank" class="px-2 py-1 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-300">Print GRN</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400 font-bold">No goods receipts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@elseif($activeTab === 'issues')

<div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
    <div class="bg-slate-800 px-4 py-3">
        <h2 class="text-xs font-bold uppercase text-white">Material Issue Register</h2>
    </div>

    <div class="p-3 bg-slate-100 border-b border-slate-300">
        <input wire:model.live.debounce.500ms="issueSearch"
               type="text"
               placeholder="Search issue number, reference, status..."
               class="w-full border border-slate-300 px-3 py-2 text-xs">
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left whitespace-nowrap">
            <thead class="bg-slate-100 text-slate-700">
                <tr>
                    <th class="px-4 py-4">Issue No.</th>
                    <th class="px-4 py-4">Date</th>
                    <th class="px-4 py-4">Project</th>
                    <th class="px-4 py-4">Items</th>
                    <th class="px-4 py-4">Value</th>
                    <th class="px-4 py-4">Status</th>
                    <th class="px-4 py-4">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse($issueTransactions as $transaction)
                    <tr>
                        <td class="px-4 py-4 font-mono font-bold">{{ $transaction->transaction_no }}</td>
                        <td class="px-4 py-4">{{ $transaction->transaction_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-4">{{ $transaction->project?->project_name ?? '-' }}</td>
                        <td class="px-4 py-4">{{ $transaction->lines->count() }}</td>
                        <td class="px-4 py-4 font-mono">{{ number_format((float) $transaction->lines->sum('line_total'), 2) }}</td>
                        <td class="px-4 py-4">{{ strtoupper($transaction->status) }}</td>
                        <td class="px-4 py-4">
                            @if(Route::has('projects.materials.issue.print'))
                                <a href="{{ route('projects.materials.issue.print', $transaction) }}" target="_blank" class="px-2 py-1 text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-300">Print Issue</a>
                            @endif

                            @if($transaction->transaction_type === 'issue_project' && ! $transaction->waybill)
                                <button type="button" wire:click="createWaybill({{ $transaction->id }})" class="px-2 py-1 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">Create Waybill</button>
                            @endif

                            @if($transaction->waybill && Route::has('projects.materials.waybill.print'))
                                <a href="{{ route('projects.materials.waybill.print', $transaction->waybill) }}" target="_blank" class="px-2 py-1 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">Print Waybill</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400 font-bold">No material issues found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@elseif($activeTab === 'waybills')

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-sm font-bold text-white">
                {{ $editingWaybillId ? 'Edit Waybill' : 'Create Waybill' }}
            </h2>
        </div>

        <form wire:submit.prevent="saveWaybill" class="p-5 space-y-4">
            <div>
                <label class="text-xs font-bold text-slate-600">Issue Transaction</label>
                <select wire:model="waybill_transaction_id"
                        class="mt-1 w-full text-xs border border-slate-300 px-2.5 py-2">
                    <option value="">Select issue transaction</option>
                    @foreach($waybillTransactions as $transaction)
                        <option value="{{ $transaction->id }}">
                            {{ $transaction->transaction_no }} — {{ $transaction->project?->project_name ?? 'No Project' }}
                        </option>
                    @endforeach
                </select>
                @error('waybill_transaction_id') <p class="text-[11px] text-red-600">{{ $message }}</p> @enderror
            </div>

            <input type="text" wire:model="transporter_name" placeholder="Transporter Name" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="driver_name" placeholder="Driver Name" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="driver_phone" placeholder="Driver Phone" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="vehicle_number" placeholder="Vehicle Number" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="delivery_location" placeholder="Delivery Location" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="loaded_by" placeholder="Loaded By" class="w-full text-xs border border-slate-300 px-2.5 py-2">
            <input type="text" wire:model="received_by" placeholder="Received By" class="w-full text-xs border border-slate-300 px-2.5 py-2">

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-green-700 text-white border border-green-800">
                    Save Waybill
                </button>

                <button type="button" wire:click="clearWaybillForm"
                        class="px-4 py-2 text-xs font-bold bg-white border border-slate-300">
                    Clear Waybill
                </button>
            </div>
        </form>
    </div>

    <div class="xl:col-span-2 border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-xs font-bold uppercase text-white">Waybill Register</h2>
        </div>

        <div class="p-3 bg-slate-100 border-b border-slate-300">
            <input wire:model.live.debounce.500ms="waybillSearch"
                   type="text"
                   placeholder="Search waybill no, transporter, driver, vehicle, project..."
                   class="w-full border border-slate-300 px-3 py-2 text-xs">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-4">Waybill No.</th>
                        <th class="px-4 py-4">Issue No.</th>
                        <th class="px-4 py-4">Project</th>
                        <th class="px-4 py-4">Transporter</th>
                        <th class="px-4 py-4">Driver</th>
                        <th class="px-4 py-4">Vehicle</th>
                        <th class="px-4 py-4">Destination</th>
                        <th class="px-4 py-4">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse($waybillTransactions as $transaction)
                        <tr>
                            <td class="px-4 py-4 font-mono font-bold">{{ $transaction->waybill?->waybill_no ?? 'NOT CREATED' }}</td>
                            <td class="px-4 py-4">{{ $transaction->transaction_no }}</td>
                            <td class="px-4 py-4">{{ $transaction->project?->project_name ?? '-' }}</td>
                            <td class="px-4 py-4">{{ $transaction->waybill?->transporter_name ?? '-' }}</td>
                            <td class="px-4 py-4">{{ $transaction->waybill?->driver_name ?? '-' }}</td>
                            <td class="px-4 py-4">{{ $transaction->waybill?->vehicle_number ?? '-' }}</td>
                            <td class="px-4 py-4">{{ $transaction->waybill?->delivery_location ?? '-' }}</td>
                            <td class="px-4 py-4">
                                @if(! $transaction->waybill)
                                    <button type="button" wire:click="createWaybill({{ $transaction->id }})" class="px-2 py-1 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">Create</button>
                                @else
                                    <button type="button" wire:click="editWaybill({{ $transaction->waybill->id }})" class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-300">Edit</button>

                                    @if(Route::has('projects.materials.waybill.print'))
                                        <a href="{{ route('projects.materials.waybill.print', $transaction->waybill) }}" target="_blank" class="px-2 py-1 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">Print</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400 font-bold">No project issue transactions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
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

        <form method="GET"
              action="{{ Route::has('projects.materials.reports.print') ? route('projects.materials.reports.print') : '#' }}"
              target="_blank"
              class="p-5 bg-slate-50 border-b border-slate-300">

            <div class="grid grid-cols-1 md:grid-cols-8 gap-3 items-end">

                <div>
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Report Type</label>
                    <select name="type"
                            id="report_type"
                            onchange="toggleReportFilters()"
                            class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                        @foreach($reportTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="project_filter_box">
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Project</label>
                    <select name="project_id"
                            id="project_id"
                            class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">
                                {{ $project->project_code }} — {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="material_filter_box">
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Material</label>
                    <select name="material_id"
                            id="material_id"
                            class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                        <option value="">All Materials</option>
                        @foreach($allMaterials as $material)
                            <option value="{{ $material->id }}">
                                {{ $material->material_code }} — {{ $material->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="status_filter_box">
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Status</label>
                    <select name="status"
                            id="status"
                            class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                        <option value="">All</option>
                        <option value="draft">Draft</option>
                        <option value="approved">Approved</option>
                        <option value="reversed">Reversed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Date From</label>
                    <input type="date"
                           name="date_from"
                           class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Date To</label>
                    <input type="date"
                           name="date_to"
                           class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Search</label>
                    <input type="text"
                           name="search"
                           placeholder="Keyword"
                           class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-600 uppercase">Action</label>
                    <select name="print"
                            class="mt-1 w-full border border-slate-300 px-2 py-2 text-xs bg-white">
                        <option value="">Preview</option>
                        <option value="1">Print</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                @if(Route::has('projects.materials.reports.print'))
                    <button type="submit"
                            class="px-5 py-2 text-xs font-bold bg-green-700 text-white border border-green-800 hover:bg-green-800">
                        Generate Report
                    </button>

                    <button type="reset"
                            class="px-5 py-2 text-xs font-bold bg-white text-slate-700 border border-slate-300 hover:bg-slate-100">
                        Clear Filters
                    </button>
                @else
                    <div class="px-4 py-2 text-xs font-bold bg-red-50 text-red-700 border border-red-300">
                        Missing route: projects.materials.reports.print
                    </div>
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Total Materials</p>
            <p class="mt-2 text-2xl font-black font-mono">{{ $materials->count() }}</p>
        </div>

        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Projects Available</p>
            <p class="mt-2 text-2xl font-black font-mono text-blue-700">{{ $projects->count() }}</p>
        </div>

        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Transactions</p>
            <p class="mt-2 text-2xl font-black font-mono text-green-700">{{ $transactions->count() }}</p>
        </div>

        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] uppercase font-bold text-slate-500">Waybills</p>
            <p class="mt-2 text-2xl font-black font-mono text-purple-700">
                {{ $waybillTransactions->filter(fn($t) => $t->waybill)->count() }}
            </p>
        </div>
    </div>
</div>

<script>
    function toggleReportFilters() {
        const type = document.getElementById('report_type')?.value;

        const projectBox = document.getElementById('project_filter_box');
        const materialBox = document.getElementById('material_filter_box');
        const statusBox = document.getElementById('status_filter_box');

        const projectSelect = document.getElementById('project_id');
        const materialSelect = document.getElementById('material_id');
        const statusSelect = document.getElementById('status');

        const projectReports = [
            'project_consumption',
            'material_issue_register',
            'waybill_register'
        ];

        const materialReports = [
            'stock_summary',
            'stock_valuation',
            'low_stock',
            'material_master',
            'material_movement',
            'material_ledger'
        ];

        const statusReports = [
            'material_movement',
            'material_ledger',
            'goods_receipt_register',
            'material_issue_register'
        ];

        const showProject = projectReports.includes(type);
        const showMaterial = materialReports.includes(type);
        const showStatus = statusReports.includes(type);

        projectBox.style.display = showProject ? 'block' : 'none';
        materialBox.style.display = showMaterial ? 'block' : 'none';
        statusBox.style.display = showStatus ? 'block' : 'none';

        if (!showProject && projectSelect) projectSelect.value = '';
        if (!showMaterial && materialSelect) materialSelect.value = '';
        if (!showStatus && statusSelect) statusSelect.value = '';
    }

    document.addEventListener('DOMContentLoaded', toggleReportFilters);
    document.addEventListener('livewire:navigated', toggleReportFilters);
</script>

@endif

</div>