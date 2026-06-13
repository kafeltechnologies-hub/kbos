<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

    <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">

        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-cyan-900 px-4 py-3 flex items-center justify-between border-b border-slate-950">
            <div>
                <span class="text-[10px] font-bold text-cyan-200 tracking-wider uppercase font-mono block">
                    Accounting Control Area
                </span>

                <h1 class="text-sm font-bold text-white">
                    Accounting Reports — GL, Cashbook, Trial Balance, Income Statement & Balance Sheet
                </h1>
            </div>
        </div>

        <div class="p-5 bg-slate-50 border-b border-slate-200 grid grid-cols-1 xl:grid-cols-4 gap-4">

            <div>
                <label class="text-[10px] font-bold uppercase text-slate-600">Report Type</label>
                <select wire:model.live="report_type"
                    class="mt-1 w-full text-xs bg-white border border-slate-300 px-2.5 py-2 shadow-inner focus:border-cyan-600 focus:ring-0 outline-none">
                    @foreach($reportTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase text-slate-600">Date From</label>
                <input type="date" wire:model.live="date_from"
                    class="mt-1 w-full text-xs bg-white border border-slate-300 px-2.5 py-2 shadow-inner focus:border-cyan-600 focus:ring-0 outline-none">
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase text-slate-600">Date To</label>
                <input type="date" wire:model.live="date_to"
                    class="mt-1 w-full text-xs bg-white border border-slate-300 px-2.5 py-2 shadow-inner focus:border-cyan-600 focus:ring-0 outline-none">
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase text-slate-600">Account Filter</label>
                <select wire:model.live="account_id"
                    class="mt-1 w-full text-xs bg-white border border-slate-300 px-2.5 py-2 shadow-inner focus:border-cyan-600 focus:ring-0 outline-none">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}">
                            {{ $account->account_code }} — {{ $account->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        @if (session()->has('info'))
            <div class="m-4 border-l-4 border-blue-600 bg-blue-50 p-3 text-xs font-medium text-blue-900 shadow-sm">
                {{ session('info') }}
            </div>
        @endif

        <div class="flex items-center gap-2 px-4 py-3 bg-white border-b border-slate-200">
            <button type="button" wire:click="sync"
                class="px-3 py-1.5 text-xs font-semibold text-cyan-700 bg-cyan-50 border border-cyan-300 hover:bg-cyan-100 shadow-sm">
                Refresh Report
            </button>

            <button type="button" onclick="window.print()"
                class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 shadow-sm">
                Print
            </button>
        </div>

    </div>

    <div class="mt-6 border border-slate-300 bg-white shadow-sm overflow-hidden">

        @if($report_type === 'general_ledger')

            <div class="bg-slate-800 px-4 py-3 border-b border-slate-900">
                <h2 class="text-xs font-bold uppercase tracking-wider text-white">
                    General Ledger
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left whitespace-nowrap table-fixed">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300">
                        <tr>
                            <th class="w-32 px-4 py-4 border-r border-slate-200">Date</th>
                            <th class="w-40 px-4 py-4 border-r border-slate-200">Account Code</th>
                            <th class="w-64 px-4 py-4 border-r border-slate-200">Account Name</th>
                            <th class="w-44 px-4 py-4 border-r border-slate-200">Reference</th>
                            <th class="w-40 px-4 py-4 border-r border-slate-200">Type</th>
                            <th class="w-72 px-4 py-4 border-r border-slate-200">Description</th>
                            <th class="w-32 px-4 py-4 border-r border-slate-200 text-right">Debit</th>
                            <th class="w-32 px-4 py-4 text-right">Credit</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($ledgerEntries as $entry)
                            <tr class="hover:bg-cyan-50/60">
                                <td class="px-4 py-4 border-r border-slate-200 font-mono">
                                    {{ $entry->posting_date }}
                                </td>

                                <td class="px-4 py-4 border-r border-slate-200 font-mono font-bold text-cyan-800">
                                    {{ $entry->account?->account_code }}
                                </td>

                                <td class="px-4 py-4 border-r border-slate-200">
                                    {{ $entry->account?->account_name }}
                                </td>

                                <td class="px-4 py-4 border-r border-slate-200 font-mono">
                                    {{ $entry->reference_no }}
                                </td>

                                <td class="px-4 py-4 border-r border-slate-200">
                                    {{ $entry->reference_type }}
                                </td>

                                <td class="px-4 py-4 border-r border-slate-200">
                                    {{ $entry->description }}
                                </td>

                                <td class="px-4 py-4 border-r border-slate-200 text-right font-mono text-green-700">
                                    {{ number_format((float) $entry->debit, 2) }}
                                </td>

                                <td class="px-4 py-4 text-right font-mono text-red-700">
                                    {{ number_format((float) $entry->credit, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                    [Err] No ledger entries found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="bg-slate-100 font-bold">
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-right border-r border-slate-300">
                                TOTAL
                            </td>
                            <td class="px-4 py-4 text-right border-r border-slate-300 font-mono">
                                {{ number_format((float) $ledgerEntries->sum('debit'), 2) }}
                            </td>
                            <td class="px-4 py-4 text-right font-mono">
                                {{ number_format((float) $ledgerEntries->sum('credit'), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        @elseif($report_type === 'cashbook')

            <div class="bg-slate-800 px-4 py-3 border-b border-slate-900">
                <h2 class="text-xs font-bold uppercase tracking-wider text-white">
                    Cashbook
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left whitespace-nowrap table-fixed">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300">
                        <tr>
                            <th class="w-32 px-4 py-4 border-r border-slate-200">Date</th>
                            <th class="w-52 px-4 py-4 border-r border-slate-200">Cash/Bank Account</th>
                            <th class="w-44 px-4 py-4 border-r border-slate-200">Reference</th>
                            <th class="w-72 px-4 py-4 border-r border-slate-200">Description</th>
                            <th class="w-32 px-4 py-4 border-r border-slate-200 text-right">Receipt</th>
                            <th class="w-32 px-4 py-4 text-right">Payment</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($cashbookEntries as $entry)
                            <tr class="hover:bg-cyan-50/60">
                                <td class="px-4 py-4 border-r border-slate-200 font-mono">
                                    {{ $entry->posting_date }}
                                </td>

                                <td class="px-4 py-4 border-r border-slate-200">
                                    {{ $entry->account?->account_code }} — {{ $entry->account?->account_name }}
                                </td>

                                <td class="px-4 py-4 border-r border-slate-200 font-mono">
                                    {{ $entry->reference_no }}
                                </td>

                                <td class="px-4 py-4 border-r border-slate-200">
                                    {{ $entry->description }}
                                </td>

                                <td class="px-4 py-4 border-r border-slate-200 text-right font-mono text-green-700">
                                    {{ number_format((float) $entry->debit, 2) }}
                                </td>

                                <td class="px-4 py-4 text-right font-mono text-red-700">
                                    {{ number_format((float) $entry->credit, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                    [Err] No cashbook entries found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="bg-slate-100 font-bold">
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-right border-r border-slate-300">
                                TOTAL
                            </td>
                            <td class="px-4 py-4 text-right border-r border-slate-300 font-mono">
                                {{ number_format((float) $cashbookEntries->sum('debit'), 2) }}
                            </td>
                            <td class="px-4 py-4 text-right font-mono">
                                {{ number_format((float) $cashbookEntries->sum('credit'), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        @elseif($report_type === 'trial_balance')

            <div class="bg-slate-800 px-4 py-3 border-b border-slate-900">
                <h2 class="text-xs font-bold uppercase tracking-wider text-white">
                    Trial Balance
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left whitespace-nowrap table-fixed">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300">
                        <tr>
                            <th class="w-36 px-4 py-4 border-r border-slate-200">Account Code</th>
                            <th class="px-4 py-4 border-r border-slate-200">Account Name</th>
                            <th class="w-36 px-4 py-4 border-r border-slate-200">Type</th>
                            <th class="w-36 px-4 py-4 border-r border-slate-200 text-right">Debit</th>
                            <th class="w-36 px-4 py-4 text-right">Credit</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($trialBalance as $row)
                            @if((float) $row['debit'] > 0 || (float) $row['credit'] > 0)
                                <tr class="hover:bg-cyan-50/60">
                                    <td class="px-4 py-4 border-r border-slate-200 font-mono font-bold text-cyan-800">
                                        {{ $row['account_code'] }}
                                    </td>

                                    <td class="px-4 py-4 border-r border-slate-200">
                                        {{ $row['account_name'] }}
                                    </td>

                                    <td class="px-4 py-4 border-r border-slate-200">
                                        {{ $row['account_type'] }}
                                    </td>

                                    <td class="px-4 py-4 border-r border-slate-200 text-right font-mono text-green-700">
                                        {{ number_format((float) $row['debit'], 2) }}
                                    </td>

                                    <td class="px-4 py-4 text-right font-mono text-red-700">
                                        {{ number_format((float) $row['credit'], 2) }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                    [Err] No trial balance data found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="bg-slate-100 font-bold">
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-right border-r border-slate-300">
                                TOTAL
                            </td>
                            <td class="px-4 py-4 text-right border-r border-slate-300 font-mono">
                                {{ number_format((float) collect($trialBalance)->sum('debit'), 2) }}
                            </td>
                            <td class="px-4 py-4 text-right font-mono">
                                {{ number_format((float) collect($trialBalance)->sum('credit'), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        @elseif($report_type === 'income_statement')

            @php
                $revenueAccounts = $incomeStatement->where('account_type', 'Revenue');
                $expenseAccounts = $incomeStatement->where('account_type', 'Expense');

                $totalRevenue = $revenueAccounts->sum(fn($a) => (float) ($a->credit_total ?? 0) - (float) ($a->debit_total ?? 0));
                $totalExpense = $expenseAccounts->sum(fn($a) => (float) ($a->debit_total ?? 0) - (float) ($a->credit_total ?? 0));
                $netProfit = $totalRevenue - $totalExpense;
            @endphp

            <div class="bg-slate-800 px-4 py-3 border-b border-slate-900">
                <h2 class="text-xs font-bold uppercase tracking-wider text-white">
                    Income Statement
                </h2>
            </div>

            <div class="p-6 space-y-8">

                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-green-700 mb-3">
                        Revenue
                    </h3>

                    <table class="w-full text-xs">
                        <tbody>
                            @foreach($revenueAccounts as $account)
                                @php
                                    $amount = (float) ($account->credit_total ?? 0) - (float) ($account->debit_total ?? 0);
                                @endphp

                                @if($amount != 0)
                                    <tr class="border-b border-slate-200">
                                        <td class="py-3 font-mono text-slate-500 w-32">
                                            {{ $account->account_code }}
                                        </td>

                                        <td class="py-3">
                                            {{ $account->account_name }}
                                        </td>

                                        <td class="py-3 text-right font-mono text-green-700">
                                            {{ number_format($amount, 2) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            <tr class="bg-green-50 font-bold">
                                <td colspan="2" class="py-3 px-3 text-right">
                                    Total Revenue
                                </td>
                                <td class="py-3 px-3 text-right font-mono">
                                    {{ number_format($totalRevenue, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-red-700 mb-3">
                        Expenses
                    </h3>

                    <table class="w-full text-xs">
                        <tbody>
                            @foreach($expenseAccounts as $account)
                                @php
                                    $amount = (float) ($account->debit_total ?? 0) - (float) ($account->credit_total ?? 0);
                                @endphp

                                @if($amount != 0)
                                    <tr class="border-b border-slate-200">
                                        <td class="py-3 font-mono text-slate-500 w-32">
                                            {{ $account->account_code }}
                                        </td>

                                        <td class="py-3">
                                            {{ $account->account_name }}
                                        </td>

                                        <td class="py-3 text-right font-mono text-red-700">
                                            {{ number_format($amount, 2) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            <tr class="bg-red-50 font-bold">
                                <td colspan="2" class="py-3 px-3 text-right">
                                    Total Expenses
                                </td>
                                <td class="py-3 px-3 text-right font-mono">
                                    {{ number_format($totalExpense, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="border border-slate-300 bg-slate-50 p-4 flex items-center justify-between">
                    <span class="text-sm font-black uppercase tracking-wider">
                        Net Profit / Loss
                    </span>

                    <span class="text-xl font-black font-mono {{ $netProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        {{ number_format($netProfit, 2) }}
                    </span>
                </div>

            </div>

        @elseif($report_type === 'balance_sheet')

            @php
                $assetAccounts = $balanceSheet->where('account_type', 'Asset');
                $liabilityAccounts = $balanceSheet->where('account_type', 'Liability');
                $equityAccounts = $balanceSheet->where('account_type', 'Equity');

                $totalAssets = $assetAccounts->sum(fn($a) => (float) ($a->debit_total ?? 0) - (float) ($a->credit_total ?? 0));
                $totalLiabilities = $liabilityAccounts->sum(fn($a) => (float) ($a->credit_total ?? 0) - (float) ($a->debit_total ?? 0));
                $totalEquity = $equityAccounts->sum(fn($a) => (float) ($a->credit_total ?? 0) - (float) ($a->debit_total ?? 0));
            @endphp

            <div class="bg-slate-800 px-4 py-3 border-b border-slate-900">
                <h2 class="text-xs font-bold uppercase tracking-wider text-white">
                    Balance Sheet
                </h2>
            </div>

            <div class="p-6 grid grid-cols-1 xl:grid-cols-3 gap-6">

                <div class="border border-slate-300 bg-white">
                    <div class="bg-blue-50 px-4 py-3 border-b border-blue-200">
                        <h3 class="text-xs font-black uppercase text-blue-700">
                            Assets
                        </h3>
                    </div>

                    <table class="w-full text-xs">
                        <tbody>
                            @foreach($assetAccounts as $account)
                                @php
                                    $amount = (float) ($account->debit_total ?? 0) - (float) ($account->credit_total ?? 0);
                                @endphp

                                @if($amount != 0)
                                    <tr class="border-b border-slate-200">
                                        <td class="py-3 px-3 font-mono text-slate-500 w-24">
                                            {{ $account->account_code }}
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $account->account_name }}
                                        </td>

                                        <td class="py-3 px-3 text-right font-mono text-blue-700">
                                            {{ number_format($amount, 2) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            <tr class="bg-blue-50 font-bold">
                                <td colspan="2" class="py-3 px-3 text-right">
                                    Total Assets
                                </td>
                                <td class="py-3 px-3 text-right font-mono">
                                    {{ number_format($totalAssets, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="border border-slate-300 bg-white">
                    <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                        <h3 class="text-xs font-black uppercase text-red-700">
                            Liabilities
                        </h3>
                    </div>

                    <table class="w-full text-xs">
                        <tbody>
                            @foreach($liabilityAccounts as $account)
                                @php
                                    $amount = (float) ($account->credit_total ?? 0) - (float) ($account->debit_total ?? 0);
                                @endphp

                                @if($amount != 0)
                                    <tr class="border-b border-slate-200">
                                        <td class="py-3 px-3 font-mono text-slate-500 w-24">
                                            {{ $account->account_code }}
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $account->account_name }}
                                        </td>

                                        <td class="py-3 px-3 text-right font-mono text-red-700">
                                            {{ number_format($amount, 2) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            <tr class="bg-red-50 font-bold">
                                <td colspan="2" class="py-3 px-3 text-right">
                                    Total Liabilities
                                </td>
                                <td class="py-3 px-3 text-right font-mono">
                                    {{ number_format($totalLiabilities, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="border border-slate-300 bg-white">
                    <div class="bg-purple-50 px-4 py-3 border-b border-purple-200">
                        <h3 class="text-xs font-black uppercase text-purple-700">
                            Equity
                        </h3>
                    </div>

                    <table class="w-full text-xs">
                        <tbody>
                            @foreach($equityAccounts as $account)
                                @php
                                    $amount = (float) ($account->credit_total ?? 0) - (float) ($account->debit_total ?? 0);
                                @endphp

                                @if($amount != 0)
                                    <tr class="border-b border-slate-200">
                                        <td class="py-3 px-3 font-mono text-slate-500 w-24">
                                            {{ $account->account_code }}
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $account->account_name }}
                                        </td>

                                        <td class="py-3 px-3 text-right font-mono text-purple-700">
                                            {{ number_format($amount, 2) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            <tr class="bg-purple-50 font-bold">
                                <td colspan="2" class="py-3 px-3 text-right">
                                    Total Equity
                                </td>
                                <td class="py-3 px-3 text-right font-mono">
                                    {{ number_format($totalEquity, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4 border-t border-slate-300 p-4 bg-slate-50">
                        <div class="flex justify-between text-xs font-black uppercase">
                            <span>Liabilities + Equity</span>
                            <span class="font-mono">
                                {{ number_format($totalLiabilities + $totalEquity, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

        @endif

    </div>

</div>