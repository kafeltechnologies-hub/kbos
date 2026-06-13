@php
    $groups = [
        'Dashboard' => [
            ['label' => 'Project Dashboard', 'route' => 'projects.dashboard', 'icon' => '🏠'],
        ],
        'Project Control' => [
            ['label' => 'Project Centre', 'route' => 'projects.project-centre', 'icon' => '🏗️'],
            ['label' => 'Projects List', 'route' => 'projects.index', 'icon' => '📁'],
            ['label' => 'Cost Entries', 'route' => 'projects.cost-entries', 'icon' => '📊'],
        ],
        'Finance Linkage' => [
            ['label' => 'Project Payments', 'route' => 'projects.payments', 'icon' => '💸'],
            ['label' => 'Project Receipts', 'route' => 'projects.receipts', 'icon' => '💰'],
            ['label' => 'Project Quotations', 'route' => 'projects.quotations', 'icon' => '📄'],
        ],
        'Finance Module' => [
            ['label' => 'Finance Dashboard', 'route' => 'finance.dashboard', 'icon' => '🏦'],
            ['label' => 'Payment Centre', 'route' => 'finance.payment-centre', 'icon' => '📤'],
            ['label' => 'Receipt Centre', 'route' => 'finance.receipt-centre', 'icon' => '📥'],
            ['label' => 'Invoice Centre', 'route' => 'finance.invoice-centre', 'icon' => '🧾'],
        ],
    ];
@endphp

<aside class="w-full lg:w-72 bg-slate-950 text-slate-100 border border-slate-800 shadow-sm">
    <div class="px-4 py-4 border-b border-slate-800 bg-slate-900">
        <p class="text-[10px] font-bold uppercase tracking-widest text-green-300">
            Project Module
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
                                    ? 'bg-green-600 text-white border-green-500'
                                    : 'bg-slate-900 text-slate-300 border-slate-800 hover:bg-slate-800 hover:text-white' }}">
                                <span class="w-5 text-center">{{ $link['icon'] }}</span>
                                <span>{{ $link['label'] }}</span>
                            </a>
                        @else
                            <div class="px-3 py-2 text-xs font-bold bg-red-950/50 text-red-300 border border-red-900">
                                ⚠️ {{ $link['label'] }}
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
