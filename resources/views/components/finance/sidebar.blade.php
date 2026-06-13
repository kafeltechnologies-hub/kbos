@php
    $groups = [
        'Dashboard' => [
            [
                'label' => 'Finance Dashboard',
                'route' => 'finance.dashboard',
                'icon' => '🏠',
            ],
        ],

        'Transactions' => [
            [
                'label' => 'Payment Vouchers',
                'route' => 'finance.payment-centre',
                'icon' => '💸',
            ],
            [
                'label' => 'Receipt Vouchers',
                'route' => 'finance.receipt-centre',
                'icon' => '💰',
            ],
            [
                'label' => 'Journal Entries',
                'route' => 'finance.journal-entries',
                'icon' => '📘',
            ],
        ],

        'Sales & Billing' => [
            [
                'label' => 'Quotations & Invoices',
                'route' => 'finance.invoice-centre',
                'icon' => '🧾',
            ],
            [
                'label' => 'Project Quotations',
                'route' => 'projects.quotations',
                'icon' => '📄',
            ],
            [
                'label' => 'Project Receipts',
                'route' => 'projects.receipts',
                'icon' => '🧾',
            ],
        ],

        'Projects' => [
            [
                'label' => 'Project Centre',
                'route' => 'projects.project-centre',
                'icon' => '🏗️',
            ],
            [
                'label' => 'Project Payments',
                'route' => 'projects.payments',
                'icon' => '📤',
            ],
            [
                'label' => 'Cost Entries',
                'route' => 'projects.cost-entries',
                'icon' => '📊',
            ],
        ],

        'Accounting' => [
            [
                'label' => 'Accounting Reports',
                'route' => 'finance.accounting-reports',
                'icon' => '📈',
            ],
            [
                'label' => 'General Ledger',
                'route' => 'finance.accounting-reports',
                'icon' => '📚',
            ],
            [
                'label' => 'Trial Balance',
                'route' => 'finance.accounting-reports',
                'icon' => '⚖️',
            ],
            [
                'label' => 'Income Statement',
                'route' => 'finance.accounting-reports',
                'icon' => '📊',
            ],
            [
                'label' => 'Balance Sheet',
                'route' => 'finance.accounting-reports',
                'icon' => '🏦',
            ],
        ],

        'Setup' => [
            [
                'label' => 'Chart of Accounts',
                'route' => 'finance.chart-of-accounts',
                'icon' => '🧮',
            ],
        ],
    ];
@endphp

<aside class="w-full lg:w-72 bg-slate-950 text-slate-100 border border-slate-800 shadow-sm">

    <div class="px-4 py-4 border-b border-slate-800 bg-slate-900">
        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-300">
            Finance Module
        </p>

        <h2 class="mt-1 text-sm font-black text-white">
            Control Centre
        </h2>
    </div>

    <nav class="p-3 space-y-4">

        @foreach($groups as $groupTitle => $links)

            <div>
                <p class="px-2 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    {{ $groupTitle }}
                </p>

                <div class="space-y-1">
                    @foreach($links as $link)

                        @if(Route::has($link['route']))
                            <a href="{{ route($link['route']) }}"
                               class="flex items-center gap-2 px-3 py-2 text-xs font-bold border
                               {{ request()->routeIs($link['route'])
                                    ? 'bg-emerald-600 text-white border-emerald-500'
                                    : 'bg-slate-900 text-slate-300 border-slate-800 hover:bg-slate-800 hover:text-white' }}">

                                <span class="w-5 text-center">
                                    {{ $link['icon'] }}
                                </span>

                                <span>
                                    {{ $link['label'] }}
                                </span>
                            </a>
                        @else
                            <div class="flex items-center gap-2 px-3 py-2 text-xs font-bold bg-red-950/50 text-red-300 border border-red-900">
                                <span class="w-5 text-center">
                                    ⚠️
                                </span>

                                <span>
                                    {{ $link['label'] }}
                                </span>
                            </div>

                            <p class="px-3 text-[9px] font-mono text-red-400">
                                Missing: {{ $link['route'] }}
                            </p>
                        @endif

                    @endforeach
                </div>
            </div>

        @endforeach

    </nav>

</aside>