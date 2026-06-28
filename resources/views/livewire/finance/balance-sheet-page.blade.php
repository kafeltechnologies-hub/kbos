<div class="min-h-screen bg-slate-100 p-6 text-slate-900">

    @include('livewire.finance._header', [
        'title' => 'Reports Centre',
        'subtitle' => 'Generate, review, print and export company financial, tax, project and management reports.'
    ])

    @include('livewire.finance._nav')

    {{-- HORIZONTAL KPI CARDS --}}
    <div class="w-full mb-6 overflow-x-auto">
        <div class="flex gap-4 min-w-max">

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Total Debit</p>
                <p class="text-2xl font-black font-mono text-green-700">
                    {{ number_format((float) ($summary['total_debit'] ?? 0), 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Total Credit</p>
                <p class="text-2xl font-black font-mono text-red-700">
                    {{ number_format((float) ($summary['total_credit'] ?? 0), 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Receipts</p>
                <p class="text-2xl font-black font-mono text-green-700">
                    {{ number_format((float) ($summary['total_receipts'] ?? 0), 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Payments</p>
                <p class="text-2xl font-black font-mono text-red-700">
                    {{ number_format((float) ($summary['total_payments'] ?? 0), 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Tax</p>
                <p class="text-2xl font-black font-mono text-amber-700">
                    {{ number_format((float) ($summary['total_tax'] ?? 0), 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Net Position</p>
                <p class="text-2xl font-black font-mono {{ (($summary['net_position'] ?? 0) >= 0) ? 'text-green-700' : 'text-red-700' }}">
                    {{ number_format((float) ($summary['net_position'] ?? 0), 2) }}
                </p>
            </div>

            <div class="w-56 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Rows</p>
                <p class="text-2xl font-black font-mono text-blue-700">
                    {{ is_countable($reportRows ?? []) ? count($reportRows ?? []) : 0 }}
                </p>
            </div>

            <div class="w-72 bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-500">Active Report</p>
                <p class="text-lg font-black text-slate-900 truncate">
                    {{ $reportTitle ?? 'Generated Report' }}
                </p>
            </div>

        </div>
    </div>

    {{-- HORIZONTAL FILTERS --}}
    <div class="bg-white border border-slate-300 p-4 mb-6 overflow-x-auto shadow-sm">
        <div class="flex gap-3 min-w-max items-center">

            <select wire:model.live="reportType"
                    class="w-72 border border-slate-300 px-3 py-2 text-xs bg-white">
                <option value="">Select Report</option>

                <optgroup label="Project Finance">
                    <option value="project_finance_summary">Project Finance Summary</option>
                    <option value="project_profitability">Project Profitability Report</option>
                    <option value="project_cost_statement">Project Cost Statement</option>
                    <option value="project_payment_report">Project Payment Report</option>
                    <option value="project_receipt_report">Project Receipt Report</option>
                    <option value="project_wip_report">Project Work In Progress</option>
                </optgroup>

                <optgroup label="Accounting Reports">
                    <option value="statement_of_account">Statement of Account</option>
                    <option value="general_ledger">General Ledger</option>
                    <option value="trial_balance">Trial Balance</option>
                    <option value="income_statement">Income Statement</option>
                    <option value="balance_sheet">Balance Sheet</option>
                    <option value="cash_flow_statement">Cash Flow Statement</option>
                    <option value="bank_reconciliation">Bank Reconciliation</option>
                </optgroup>

                <optgroup label="Tax Reports">
                    <option value="vat_report">VAT Report</option>
                    <option value="withholding_tax_report">Withholding Tax Report</option>
                    <option value="nhil_getfund_report">NHIL / GETFund Report</option>
                    <option value="paye_report">PAYE Report</option>
                    <option value="ssnit_report">SSNIT Report</option>
                    <option value="tax_payment_summary">Tax Payment Summary</option>
                </optgroup>

                <optgroup label="Receivables & Payables">
                    <option value="accounts_receivable">Accounts Receivable</option>
                    <option value="accounts_payable">Accounts Payable</option>
                    <option value="customer_statement">Customer Statement</option>
                    <option value="supplier_statement">Supplier Statement</option>
                    <option value="aging_receivables">Receivables Aging</option>
                    <option value="aging_payables">Payables Aging</option>
                </optgroup>

                <optgroup label="Management Reports">
                    <option value="cash_position">Cash Position</option>
                    <option value="daily_finance_summary">Daily Finance Summary</option>
                    <option value="budget_variance">Budget Variance</option>
                    <option value="expense_analysis">Expense Analysis</option>
                    <option value="fixed_asset_report">Fixed Asset Report</option>
                    <option value="audit_trail">Audit Trail</option>
                </optgroup>
            </select>

            <input type="text"
                   wire:model.live.debounce.500ms="search"
                   placeholder="Search reference, account, narration..."
                   class="w-72 border border-slate-300 px-3 py-2 text-xs">

            <input type="date"
                   wire:model.live="fromDate"
                   class="w-44 border border-slate-300 px-3 py-2 text-xs">

            <input type="date"
                   wire:model.live="toDate"
                   class="w-44 border border-slate-300 px-3 py-2 text-xs">

            <select wire:model.live="projectId"
                    class="w-64 border border-slate-300 px-3 py-2 text-xs bg-white">
                <option value="">All Projects</option>
                @foreach($projects ?? [] as $project)
                    <option value="{{ $project->id }}">
                        {{ $project->project_name ?? $project->name ?? 'Unnamed Project' }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="accountId"
                    class="w-64 border border-slate-300 px-3 py-2 text-xs bg-white">
                <option value="">All Accounts</option>
                @foreach($accounts ?? [] as $account)
                    <option value="{{ $account->id }}">
                        {{ $account->account_code ?? $account->code ?? '' }}
                        —
                        {{ $account->account_name ?? $account->name ?? 'Unnamed Account' }}
                    </option>
                @endforeach
            </select>

            <button type="button"
                    wire:click="generateReport"
                    class="w-36 bg-slate-900 text-white border border-slate-900 px-4 py-2 text-xs font-bold">
                Generate
            </button>

            <button type="button"
                    wire:click="resetFilters"
                    class="w-36 bg-white border border-slate-300 px-4 py-2 text-xs font-bold">
                Clear Filters
            </button>

        </div>
    </div>

    {{-- COMPANY AND PRINT INFORMATION --}}
    <div class="bg-white border border-slate-300 shadow-sm overflow-hidden mb-6">

        <div class="bg-slate-900 text-white px-4 py-3">
            <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">
                Company / Tax Information
            </p>
            <h2 class="text-sm font-black">
                Print Header Details
            </h2>
        </div>

        <div class="w-full overflow-x-auto p-5">
            <div class="flex gap-4 min-w-max">

                <div class="w-72 border border-slate-300 bg-slate-50 p-4">
                    <p class="text-[10px] uppercase font-bold text-slate-500">Company</p>
                    <p class="text-sm font-black">
                        {{ $company->name ?? $company['name'] ?? config('app.name') }}
                    </p>
                </div>

                <div class="w-64 border border-slate-300 bg-slate-50 p-4">
                    <p class="text-[10px] uppercase font-bold text-slate-500">TIN</p>
                    <p class="text-sm font-black font-mono">
                        {{ $company->tin ?? $company['tin'] ?? 'N/A' }}
                    </p>
                </div>

                <div class="w-64 border border-slate-300 bg-slate-50 p-4">
                    <p class="text-[10px] uppercase font-bold text-slate-500">VAT Number</p>
                    <p class="text-sm font-black font-mono">
                        {{ $company->vat_number ?? $company['vat_number'] ?? 'N/A' }}
                    </p>
                </div>

                <div class="w-64 border border-slate-300 bg-slate-50 p-4">
                    <p class="text-[10px] uppercase font-bold text-slate-500">Period</p>
                    <p class="text-sm font-black font-mono">
                        {{ !empty($fromDate) ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }}
                        -
                        {{ !empty($toDate) ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'Today' }}
                    </p>
                </div>

                <div class="w-64 border border-slate-300 bg-slate-50 p-4">
                    <p class="text-[10px] uppercase font-bold text-slate-500">Printed By</p>
                    <p class="text-sm font-black">
                        {{ auth()->user()->name ?? 'System User' }}
                    </p>
                </div>

                <div class="w-64 border border-slate-300 bg-slate-50 p-4">
                    <p class="text-[10px] uppercase font-bold text-slate-500">Generated At</p>
                    <p class="text-sm font-black font-mono">
                        {{ now()->format('d M Y h:i A') }}
                    </p>
                </div>

            </div>
        </div>

        <div class="px-5 pb-5">
            <div class="border border-slate-300 bg-green-50 p-4">
                <p class="text-xs font-black text-green-800">
                    Reports printed from this centre will carry company details, tax identification, report period, preparer and transaction references.
                </p>
            </div>
        </div>

    </div>

    {{-- PRINT CENTRE --}}
    <div class="bg-white border border-slate-300 shadow-sm overflow-hidden mb-6">

        <div class="bg-slate-800 text-white px-4 py-3">
            <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">
                Print Centre
            </p>
            <h2 class="text-sm font-black">
                Full Report, PDF, Excel And Transaction Printouts
            </h2>
        </div>

        <div class="p-5 overflow-x-auto">
            <div class="flex gap-3 min-w-max">

                <button type="button"
                        wire:click="printReport"
                        class="w-44 bg-green-700 text-white border border-green-800 px-4 py-2 text-xs font-bold">
                    Print Full Report
                </button>

                <button type="button"
                        wire:click="exportPdf"
                        class="w-36 bg-blue-700 text-white border border-blue-800 px-4 py-2 text-xs font-bold">
                    Export PDF
                </button>

                <button type="button"
                        wire:click="exportExcel"
                        class="w-36 bg-amber-500 text-slate-900 border border-amber-600 px-4 py-2 text-xs font-bold">
                    Export Excel
                </button>

                <button type="button"
                        wire:click="$refresh"
                        class="w-36 bg-white border border-slate-300 px-4 py-2 text-xs font-bold">
                    Refresh
                </button>

                <button type="button"
                        wire:click="resetFilters"
                        class="w-36 bg-white border border-slate-300 px-4 py-2 text-xs font-bold">
                    Reset
                </button>

            </div>
        </div>
    </div>

    {{-- MAIN REPORT TABLE --}}
    <div class="bg-white border border-slate-300 shadow-sm overflow-hidden mb-6">

        <div class="bg-slate-900 text-white px-4 py-3">
            <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">
                Accounting Reports
            </p>
            <h2 class="text-sm font-black">
                {{ $reportTitle ?? 'Generated Report' }}
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Reference</th>
                        <th class="p-3 text-left">Project / Account</th>
                        <th class="p-3 text-left">Description</th>
                        <th class="p-3 text-right">Debit</th>
                        <th class="p-3 text-right">Credit</th>
                        <th class="p-3 text-right">Tax</th>
                        <th class="p-3 text-right">Balance</th>
                        <th class="p-3 text-left">Source</th>
                        <th class="p-3 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @php
                        $tableDebit = 0;
                        $tableCredit = 0;
                        $tableTax = 0;
                        $tableBalance = 0;
                    @endphp

                    @forelse($reportRows ?? [] as $row)

                        @php
                            $isArray = is_array($row);

                            $rowId = $isArray ? ($row['id'] ?? null) : ($row->id ?? null);

                            $date = $isArray
                                ? ($row['date'] ?? null)
                                : ($row->posting_date ?? $row->entry_date ?? $row->transaction_date ?? $row->created_at ?? null);

                            $reference = $isArray
                                ? ($row['reference'] ?? 'N/A')
                                : ($row->reference ?? $row->reference_no ?? $row->voucher_no ?? 'N/A');

                            $projectAccount = $isArray
                                ? ($row['project_account'] ?? $row['project_name'] ?? $row['account_name'] ?? 'N/A')
                                : ($row->project_name ?? $row->account_name ?? $row->account ?? 'N/A');

                            $description = $isArray
                                ? ($row['description'] ?? $row['narration'] ?? 'No description')
                                : ($row->description ?? $row->narration ?? $row->remarks ?? 'No description');

                            $source = $isArray
                                ? ($row['source'] ?? 'report')
                                : ($row->source ?? $row->module ?? 'report');

                            $debit = (float) ($isArray
                                ? ($row['debit'] ?? 0)
                                : ($row->debit ?? $row->debit_amount ?? 0));

                            $credit = (float) ($isArray
                                ? ($row['credit'] ?? 0)
                                : ($row->credit ?? $row->credit_amount ?? 0));

                            $tax = (float) ($isArray
                                ? ($row['tax'] ?? 0)
                                : ($row->tax_amount ?? $row->vat_amount ?? $row->withholding_tax ?? 0));

                            $balance = (float) ($isArray
                                ? ($row['balance'] ?? (($debit - $credit) - $tax))
                                : (($debit - $credit) - $tax));

                            $tableDebit += $debit;
                            $tableCredit += $credit;
                            $tableTax += $tax;
                            $tableBalance += $balance;
                        @endphp

                        <tr class="hover:bg-slate-50">

                            <td class="p-3 whitespace-nowrap text-slate-600">
                                {{ $date ? \Carbon\Carbon::parse($date)->format('d M Y') : 'N/A' }}
                            </td>

                            <td class="p-3 font-mono font-bold text-green-700 whitespace-nowrap">
                                {{ $reference }}
                            </td>

                            <td class="p-3 font-bold text-slate-900">
                                {{ $projectAccount }}
                            </td>

                            <td class="p-3 text-slate-600 max-w-lg">
                                {{ $description }}
                            </td>

                            <td class="p-3 text-right font-mono text-green-700">
                                {{ $debit > 0 ? number_format($debit, 2) : '-' }}
                            </td>

                            <td class="p-3 text-right font-mono text-red-700">
                                {{ $credit > 0 ? number_format($credit, 2) : '-' }}
                            </td>

                            <td class="p-3 text-right font-mono text-amber-700">
                                {{ $tax > 0 ? number_format($tax, 2) : '-' }}
                            </td>

                            <td class="p-3 text-right font-mono font-black {{ $balance >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                {{ number_format($balance, 2) }}
                            </td>

                            <td class="p-3">
                                <span class="px-2 py-1 text-[10px] font-bold uppercase border bg-slate-50 border-slate-300">
                                    {{ str_replace('_', ' ', $source) }}
                                </span>
                            </td>

                            <td class="p-3 text-center">
                                @if($rowId)
                                    <button type="button"
                                            wire:click="printSingleTransaction({{ $rowId }})"
                                            class="px-3 py-1 bg-slate-900 text-white text-[10px] font-bold uppercase">
                                        Print
                                    </button>
                                @else
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">N/A</span>
                                @endif
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="10" class="p-8 text-center text-slate-400 font-bold">
                                No report rows found. Select a report type and click Generate.
                            </td>
                        </tr>

                    @endforelse
                </tbody>

                <tfoot class="bg-slate-100 font-black">
                    <tr>
                        <td colspan="4" class="p-3 text-right">Totals</td>

                        <td class="p-3 text-right font-mono text-green-700">
                            {{ number_format($tableDebit ?? 0, 2) }}
                        </td>

                        <td class="p-3 text-right font-mono text-red-700">
                            {{ number_format($tableCredit ?? 0, 2) }}
                        </td>

                        <td class="p-3 text-right font-mono text-amber-700">
                            {{ number_format($tableTax ?? 0, 2) }}
                        </td>

                        <td class="p-3 text-right font-mono {{ ($tableBalance ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            {{ number_format($tableBalance ?? 0, 2) }}
                        </td>

                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if(isset($reportRows) && is_object($reportRows) && method_exists($reportRows, 'links'))
            <div class="p-4 border-t border-slate-300">
                {{ $reportRows->links() }}
            </div>
        @endif

    </div>

    {{-- REPORT ANALYSIS PANELS --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- TAX SUMMARY --}}
        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">

            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-amber-300 font-bold">Tax Summary</p>
                <h2 class="text-sm font-black">VAT, WHT, NHIL, GETFund And Statutory Exposure</h2>
            </div>

            <div class="divide-y divide-slate-200">
                <div class="p-4 flex justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold">Total Tax Captured</p>
                        <p class="text-[10px] text-slate-500">From selected report rows</p>
                    </div>

                    <p class="font-mono font-black text-amber-700">
                        {{ number_format((float) ($summary['total_tax'] ?? 0), 2) }}
                    </p>
                </div>

                <div class="p-4 flex justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold">Tax Report Selected</p>
                        <p class="text-[10px] text-slate-500">Current report type</p>
                    </div>

                    <p class="font-black text-slate-900">
                        {{ str_replace('_', ' ', strtoupper($reportType ?? 'NONE')) }}
                    </p>
                </div>

                <div class="p-4 bg-amber-50">
                    <p class="text-xs font-black text-amber-800">
                        Use the Print Centre to generate tax-ready reports with company TIN, VAT number and report period.
                    </p>
                </div>
            </div>
        </div>

        {{-- CASH / NET POSITION --}}
        <div class="bg-white border border-slate-300 shadow-sm overflow-hidden">

            <div class="bg-slate-900 text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Cash / Net Position</p>
                <h2 class="text-sm font-black">Receipts, Payments And Net Movement</h2>
            </div>

            <div class="divide-y divide-slate-200">
                <div class="p-4 flex justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold">Receipts / Debits</p>
                        <p class="text-[10px] text-slate-500">Cash inflow or debit-side activity</p>
                    </div>

                    <p class="font-mono font-black text-green-700">
                        {{ number_format((float) ($summary['total_receipts'] ?? 0), 2) }}
                    </p>
                </div>

                <div class="p-4 flex justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold">Payments / Credits</p>
                        <p class="text-[10px] text-slate-500">Cash outflow or credit-side activity</p>
                    </div>

                    <p class="font-mono font-black text-red-700">
                        {{ number_format((float) ($summary['total_payments'] ?? 0), 2) }}
                    </p>
                </div>

                <div class="p-4 bg-slate-900 text-white flex justify-between font-black text-sm">
                    <span>NET POSITION</span>
                    <span class="font-mono">
                        {{ number_format((float) ($summary['net_position'] ?? 0), 2) }}
                    </span>
                </div>
            </div>
        </div>

    </div>

</div>