<div class="min-h-screen bg-slate-100 p-6 text-slate-900">

    @include('livewire.finance._header', [
        'title' => 'Finance Settings',
        'subtitle' => 'Configure default accounts, tax rates, posting controls and finance synchronization rules.'
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

    {{-- KPI / STATUS CARDS --}}
    <div class="w-full mb-6 overflow-x-auto">
        <div class="flex gap-4 min-w-max">

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">GL Sync</p>
                <p class="text-2xl font-black font-mono {{ ($glSyncEnabled ?? true) ? 'text-green-700' : 'text-red-700' }}">
                    {{ ($glSyncEnabled ?? true) ? 'ON' : 'OFF' }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Tax Engine</p>
                <p class="text-2xl font-black font-mono {{ ($taxEnabled ?? true) ? 'text-green-700' : 'text-red-700' }}">
                    {{ ($taxEnabled ?? true) ? 'ON' : 'OFF' }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Accounts</p>
                <p class="text-2xl font-black font-mono">{{ $accounts?->count() ?? 0 }}</p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Fiscal Year</p>
                <p class="text-2xl font-black font-mono text-blue-700">{{ $financial_year ?? now()->year }}</p>
            </div>

        </div>
    </div>

    <form wire:submit.prevent="saveSettings" class="space-y-6">

        {{-- DEFAULT ACCOUNTS --}}
        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">

            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Default Accounts</p>
                <h2 class="text-sm font-black">FinanceCoordinator Posting Accounts</h2>
            </div>

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

                @foreach([
                    'cash_account_id' => 'Cash / Bank Account',
                    'accounts_receivable_id' => 'Accounts Receivable',
                    'accounts_payable_id' => 'Accounts Payable',
                    'inventory_account_id' => 'Inventory Asset',
                    'fixed_asset_account_id' => 'Fixed Assets',
                    'accumulated_depreciation_account_id' => 'Accumulated Depreciation',
                    'depreciation_expense_account_id' => 'Depreciation Expense',
                    'sales_revenue_account_id' => 'Sales Revenue',
                    'material_revenue_account_id' => 'Material Revenue',
                    'project_cost_account_id' => 'Project Cost',
                    'tax_payable_account_id' => 'Tax Payable',
                    'withholding_tax_account_id' => 'Withholding Tax Payable',
                ] as $field => $label)

                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">{{ $label }}</label>
                        <select wire:model="{{ $field }}"
                                class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="">Select Account</option>
                            @foreach($accounts ?? [] as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->account_code }} — {{ $account->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                @endforeach

            </div>
        </div>

        {{-- TAX RATES --}}
        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">

            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Tax Configuration</p>
                <h2 class="text-sm font-black">Default Ghana Tax Rates</h2>
            </div>

            <div class="p-5 overflow-x-auto">
                <div class="flex gap-4 min-w-max">

                    <div class="w-48">
                        <label class="text-[10px] uppercase font-bold text-slate-500">VAT %</label>
                        <input type="number" step="0.01" wire:model="vat_rate"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div class="w-48">
                        <label class="text-[10px] uppercase font-bold text-slate-500">NHIL %</label>
                        <input type="number" step="0.01" wire:model="nhil_rate"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div class="w-48">
                        <label class="text-[10px] uppercase font-bold text-slate-500">GETFund %</label>
                        <input type="number" step="0.01" wire:model="getfund_rate"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div class="w-48">
                        <label class="text-[10px] uppercase font-bold text-slate-500">WHT %</label>
                        <input type="number" step="0.01" wire:model="wht_rate"
                               class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs">
                    </div>

                    <div class="w-48">
                        <label class="text-[10px] uppercase font-bold text-slate-500">PAYE Enabled</label>
                        <select wire:model="paye_enabled"
                                class="mt-1 w-full border border-slate-300 px-3 py-2 text-xs bg-white">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        {{-- POSTING RULES --}}
        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">

            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Posting Controls</p>
                <h2 class="text-sm font-black">Module Synchronization Rules</h2>
            </div>

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

                @foreach([
                    'auto_post_fixed_assets' => 'Auto-post Fixed Assets',
                    'auto_post_materials' => 'Auto-post Materials',
                    'auto_post_receipts' => 'Auto-post Receipts',
                    'auto_post_payments' => 'Auto-post Payments',
                    'auto_post_invoices' => 'Auto-post Invoices',
                    'lock_posted_entries' => 'Lock Posted Entries',
                    'require_approval_before_posting' => 'Require Approval Before Posting',
                    'allow_backdated_posting' => 'Allow Backdated Posting',
                ] as $field => $label)

                    <label class="flex items-center gap-2 text-xs font-bold bg-slate-50 border border-slate-300 p-3">
                        <input type="checkbox" wire:model="{{ $field }}">
                        {{ $label }}
                    </label>

                @endforeach

            </div>
        </div>

        {{-- SAVE --}}
        <div class="bg-white border border-slate-300 p-4 flex flex-wrap gap-2">
            <button type="submit"
                    class="bg-green-700 text-white border border-green-800 px-5 py-2 text-xs font-bold">
                Save Finance Settings
            </button>

            <button type="button"
                    wire:click="resetSettings"
                    class="bg-white border border-slate-300 px-5 py-2 text-xs font-bold">
                Reset
            </button>
        </div>

    </form>
</div>