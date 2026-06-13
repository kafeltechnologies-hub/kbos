
<div class="min-h-screen bg-slate-100 text-slate-900 p-6">

    <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-900 px-5 py-4 border-b border-slate-950">
            <span class="text-[10px] font-bold text-emerald-200 tracking-wider uppercase font-mono block">
                Finance Control Centre
            </span>

            <h1 class="text-lg font-black text-white">
                Finance Dashboard
            </h1>

            <p class="text-xs text-slate-300 mt-1">
                One page for payments, receipts, invoices, quotations, journals, reports and accounting navigation.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mt-6">

        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Contract Value</p>
            <p class="mt-2 text-2xl font-black font-mono text-green-800">
                {{ number_format((float) $contractValue, 2) }}
            </p>
        </div>

        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Payments Made</p>
            <p class="mt-2 text-2xl font-black font-mono text-red-700">
                {{ number_format((float) $totalPayments, 2) }}
            </p>
        </div>

        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Receipts Received</p>
            <p class="mt-2 text-2xl font-black font-mono text-blue-800">
                {{ number_format((float) $totalReceipts, 2) }}
            </p>
        </div>

        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Invoices Raised</p>
            <p class="mt-2 text-2xl font-black font-mono text-indigo-800">
                {{ number_format((float) $totalInvoices, 2) }}
            </p>
        </div>

        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Outstanding Receivables</p>
            <p class="mt-2 text-2xl font-black font-mono text-amber-800">
                {{ number_format((float) $outstandingReceivables, 2) }}
            </p>
        </div>

        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Net Cash Position</p>
            <p class="mt-2 text-2xl font-black font-mono {{ $netCashPosition >= 0 ? 'text-green-800' : 'text-red-700' }}">
                {{ number_format((float) $netCashPosition, 2) }}
            </p>
        </div>

        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">Projects</p>
            <p class="mt-2 text-2xl font-black font-mono text-slate-900">
                {{ $totalProjects }}
            </p>
        </div>

        <div class="bg-white border border-slate-300 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-slate-500">GL Entries</p>
            <p class="mt-2 text-2xl font-black font-mono text-purple-800">
                {{ $glEntries }}
            </p>
        </div>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-8">

        <div class="border border-slate-300 bg-white shadow-sm">
            <div class="bg-slate-800 px-4 py-3">
                <h2 class="text-xs font-bold uppercase text-white">Transactions</h2>
            </div>

            <div class="p-4 grid gap-3">
                <a href="{{ route('finance.payment-centre') }}" class="px-4 py-3 bg-red-50 border border-red-200 text-red-800 font-bold text-xs hover:bg-red-100">
                    Payment Vouchers
                </a>

                <a href="{{ route('finance.receipt-centre') }}" class="px-4 py-3 bg-blue-50 border border-blue-200 text-blue-800 font-bold text-xs hover:bg-blue-100">
                    Receipt Vouchers
                </a>

                <a href="{{ route('finance.journal-entries') }}" class="px-4 py-3 bg-purple-50 border border-purple-200 text-purple-800 font-bold text-xs hover:bg-purple-100">
                    Journal Entries
                </a>
            </div>
        </div>

        <div class="border border-slate-300 bg-white shadow-sm">
            <div class="bg-slate-800 px-4 py-3">
                <h2 class="text-xs font-bold uppercase text-white">Sales & Billing</h2>
            </div>

            <div class="p-4 grid gap-3">
                <a href="{{ route('finance.invoice-centre') }}" class="px-4 py-3 bg-indigo-50 border border-indigo-200 text-indigo-800 font-bold text-xs hover:bg-indigo-100">
                    Quotations & Invoices
                </a>

                <a href="{{ route('finance.receipt-centre') }}" class="px-4 py-3 bg-green-50 border border-green-200 text-green-800 font-bold text-xs hover:bg-green-100">
                    Customer Receipts
                </a>

                <a href="{{ route('finance.accounting-reports') }}" class="px-4 py-3 bg-amber-50 border border-amber-200 text-amber-800 font-bold text-xs hover:bg-amber-100">
                    Accounts Receivable
                </a>
            </div>
        </div>

        <div class="border border-slate-300 bg-white shadow-sm">
            <div class="bg-slate-800 px-4 py-3">
                <h2 class="text-xs font-bold uppercase text-white">Accounting</h2>
            </div>

            <div class="p-4 grid gap-3">
                <a href="{{ route('finance.accounting-reports') }}" class="px-4 py-3 bg-cyan-50 border border-cyan-200 text-cyan-800 font-bold text-xs hover:bg-cyan-100">
                    Accounting Reports
                </a>

                <a href="{{ route('finance.accounting-reports') }}" class="px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 font-bold text-xs hover:bg-slate-100">
                    General Ledger
                </a>

                <a href="{{ route('finance.accounting-reports') }}" class="px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 font-bold text-xs hover:bg-slate-100">
                    Trial Balance
                </a>
            </div>
        </div>

        <div class="border border-slate-300 bg-white shadow-sm">
            <div class="bg-slate-800 px-4 py-3">
                <h2 class="text-xs font-bold uppercase text-white">Projects</h2>
            </div>

            <div class="p-4 grid gap-3">
                <a href="{{ route('projects.project-centre') }}" class="px-4 py-3 bg-green-50 border border-green-200 text-green-800 font-bold text-xs hover:bg-green-100">
                    Project Centre
                </a>

                <a href="{{ route('finance.payment-centre') }}" class="px-4 py-3 bg-red-50 border border-red-200 text-red-800 font-bold text-xs hover:bg-red-100">
                    Project Payments
                </a>

                <a href="{{ route('finance.receipt-centre') }}" class="px-4 py-3 bg-blue-50 border border-blue-200 text-blue-800 font-bold text-xs hover:bg-blue-100">
                    Project Receipts
                </a>
            </div>
        </div>

        <div class="border border-slate-300 bg-white shadow-sm">
            <div class="bg-slate-800 px-4 py-3">
                <h2 class="text-xs font-bold uppercase text-white">Reports</h2>
            </div>

            <div class="p-4 grid gap-3">
                <a href="{{ route('finance.accounting-reports') }}" class="px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 font-bold text-xs hover:bg-slate-100">
                    Income Statement
                </a>

                <a href="{{ route('finance.accounting-reports') }}" class="px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 font-bold text-xs hover:bg-slate-100">
                    Balance Sheet
                </a>

                <a href="{{ route('finance.accounting-reports') }}" class="px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 font-bold text-xs hover:bg-slate-100">
                    Cashbook
                </a>
            </div>
        </div>

        <div class="border border-slate-300 bg-white shadow-sm">
            <div class="bg-slate-800 px-4 py-3">
                <h2 class="text-xs font-bold uppercase text-white">Setup</h2>
            </div>

            <div class="p-4 grid gap-3">
                <a href="{{ route('finance.accounting-reports') }}" class="px-4 py-3 bg-purple-50 border border-purple-200 text-purple-800 font-bold text-xs hover:bg-purple-100">
                    Chart of Accounts
                </a>

                <a href="{{ route('finance.payment-centre') }}" class="px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 font-bold text-xs hover:bg-slate-100">
                    Expense Categories
                </a>

                <a href="{{ route('finance.receipt-centre') }}" class="px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 font-bold text-xs hover:bg-slate-100">
                    Income Categories
                </a>
            </div>
        </div>

    </div>

</div>