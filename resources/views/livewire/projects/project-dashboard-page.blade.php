<div class="min-h-screen bg-slate-100 p-6 text-slate-900">

    @php
        $projectNavLinks = [
            ['label' => 'Dashboard', 'route' => 'projects.dashboard'],
            ['label' => 'Project Centre', 'route' => 'projects.project-centre'],
            ['label' => 'Projects List', 'route' => 'projects.index'],
            ['label' => 'Cost Entries', 'route' => 'projects.cost-entries'],
            ['label' => 'Payments', 'route' => 'projects.payments'],
            ['label' => 'Receipts', 'route' => 'projects.receipts'],
            ['label' => 'Quotations', 'route' => 'projects.quotations'],
            ['label' => 'Finance Dashboard', 'route' => 'finance.dashboard'],
            ['label' => 'Materials / Inventory', 'route' => 'projects.materials'],
        ];
    @endphp

    <div class="space-y-6">

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-green-900 px-5 py-4">
                <span class="text-[10px] font-bold text-green-200 tracking-wider uppercase font-mono block">
                    Project Control Centre
                </span>

                <h1 class="text-lg font-black text-white">
                    Project Dashboard
                </h1>

                <p class="text-xs text-slate-300 mt-1">
                    One page for scope, phases, WBS, deliverables, materials, budget, costing and project finance.
                </p>
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

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    Total Projects
                </p>

                <p class="mt-2 text-2xl font-black font-mono text-slate-900">
                    {{ $totalProjects }}
                </p>
            </div>

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    Active Projects
                </p>

                <p class="mt-2 text-2xl font-black font-mono text-green-800">
                    {{ $activeProjects }}
                </p>
            </div>

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    Completed Projects
                </p>

                <p class="mt-2 text-2xl font-black font-mono text-blue-800">
                    {{ $completedProjects }}
                </p>
            </div>

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    Project Phases
                </p>

                <p class="mt-2 text-2xl font-black font-mono text-purple-800">
                    {{ $phaseCount }}
                </p>
            </div>

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    Contract Value
                </p>

                <p class="mt-2 text-2xl font-black font-mono text-green-800">
                    {{ number_format((float) $contractValue, 2) }}
                </p>
            </div>

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    Estimated Cost
                </p>

                <p class="mt-2 text-2xl font-black font-mono text-red-700">
                    {{ number_format((float) $estimatedCost, 2) }}
                </p>
            </div>

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    Expected Profit
                </p>

                <p class="mt-2 text-2xl font-black font-mono {{ (float) $expectedProfit >= 0 ? 'text-blue-800' : 'text-red-700' }}">
                    {{ number_format((float) $expectedProfit, 2) }}
                </p>
            </div>

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    Budget Variance
                </p>

                <p class="mt-2 text-2xl font-black font-mono {{ (float) $budgetVariance >= 0 ? 'text-green-800' : 'text-red-700' }}">
                    {{ number_format((float) $budgetVariance, 2) }}
                </p>
            </div>

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    WBS Items
                </p>

                <p class="mt-2 text-2xl font-black font-mono text-cyan-800">
                    {{ $wbsCount }}
                </p>
            </div>

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    Deliverables
                </p>

                <p class="mt-2 text-2xl font-black font-mono text-indigo-800">
                    {{ $deliverableCount }}
                </p>
            </div>

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    Material Lines
                </p>

                <p class="mt-2 text-2xl font-black font-mono text-amber-800">
                    {{ $materialCount }}
                </p>
            </div>

            <div class="bg-white border border-slate-300 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-500">
                    Average Progress
                </p>

                <p class="mt-2 text-2xl font-black font-mono text-slate-900">
                    {{ number_format((float) $averageProgress, 2) }}%
                </p>
            </div>

        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="border border-slate-300 bg-white shadow-sm">
                <div class="bg-slate-800 px-4 py-3">
                    <h2 class="text-xs font-bold uppercase text-white">
                        Project Planning
                    </h2>
                </div>

                <div class="p-4 grid gap-3">
                    @if(Route::has('projects.project-centre'))
                        <a href="{{ route('projects.project-centre') }}"
                           class="px-4 py-3 bg-green-50 border border-green-200 text-green-800 font-bold text-xs hover:bg-green-100">
                            Create / Define Project
                        </a>

                        <a href="{{ route('projects.project-centre') }}"
                           class="px-4 py-3 bg-blue-50 border border-blue-200 text-blue-800 font-bold text-xs hover:bg-blue-100">
                            Define Project Scope
                        </a>

                        <a href="{{ route('projects.project-centre') }}"
                           class="px-4 py-3 bg-purple-50 border border-purple-200 text-purple-800 font-bold text-xs hover:bg-purple-100">
                            Define Project Phases
                        </a>
                    @endif
                </div>
            </div>

            <div class="border border-slate-300 bg-white shadow-sm">
                <div class="bg-slate-800 px-4 py-3">
                    <h2 class="text-xs font-bold uppercase text-white">
                        Execution Control
                    </h2>
                </div>

                <div class="p-4 grid gap-3">
                    @if(Route::has('projects.project-centre'))
                        <a href="{{ route('projects.project-centre') }}"
                           class="px-4 py-3 bg-cyan-50 border border-cyan-200 text-cyan-800 font-bold text-xs hover:bg-cyan-100">
                            Work Breakdown Structure
                        </a>

                        <a href="{{ route('projects.materials') }}"
                        class="px-4 py-3 bg-amber-50 border border-amber-200 text-amber-800 font-bold text-xs hover:bg-amber-100">
                            Project Materials / Inventory
                        </a>
                    @endif

                    @if(Route::has('projects.cost-entries'))
                        <a href="{{ route('projects.cost-entries') }}"
                           class="px-4 py-3 bg-red-50 border border-red-200 text-red-800 font-bold text-xs hover:bg-red-100">
                            Cost Entries
                        </a>
                    @endif
                </div>
            </div>

            <div class="border border-slate-300 bg-white shadow-sm">
                <div class="bg-slate-800 px-4 py-3">
                    <h2 class="text-xs font-bold uppercase text-white">
                        Project Finance
                    </h2>
                </div>

                <div class="p-4 grid gap-3">
                    @if(Route::has('projects.quotations'))
                        <a href="{{ route('projects.quotations') }}"
                           class="px-4 py-3 bg-indigo-50 border border-indigo-200 text-indigo-800 font-bold text-xs hover:bg-indigo-100">
                            Project Quotations
                        </a>
                    @endif

                    @if(Route::has('projects.payments'))
                        <a href="{{ route('projects.payments') }}"
                           class="px-4 py-3 bg-red-50 border border-red-200 text-red-800 font-bold text-xs hover:bg-red-100">
                            Project Payments
                        </a>
                    @endif

                    @if(Route::has('projects.receipts'))
                        <a href="{{ route('projects.receipts') }}"
                           class="px-4 py-3 bg-blue-50 border border-blue-200 text-blue-800 font-bold text-xs hover:bg-blue-100">
                            Project Receipts
                        </a>
                    @endif
                </div>
            </div>

        </div>

        <div class="border border-slate-300 bg-white shadow-sm overflow-hidden">
            <div class="bg-slate-800 px-4 py-3 border-b border-slate-900">
                <h2 class="text-xs font-bold uppercase tracking-wider text-white">
                    Recent Projects
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left whitespace-nowrap">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300">
                        <tr>
                            <th class="px-4 py-4 border-r border-slate-200">Code</th>
                            <th class="px-4 py-4 border-r border-slate-200">Project</th>
                            <th class="px-4 py-4 border-r border-slate-200">Company</th>
                            <th class="px-4 py-4 border-r border-slate-200">Client</th>
                            <th class="px-4 py-4 border-r border-slate-200">Contract Value</th>
                            <th class="px-4 py-4 border-r border-slate-200">Cost</th>
                            <th class="px-4 py-4 border-r border-slate-200">Status</th>
                            <th class="px-4 py-4">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 font-medium">
                        @forelse($recentProjects as $project)
                            <tr class="hover:bg-green-50/70">
                                <td class="px-4 py-5 font-mono font-bold text-green-800 border-r border-slate-200">
                                    {{ $project->project_code }}
                                </td>

                                <td class="px-4 py-5 border-r border-slate-200">
                                    <div class="font-bold text-slate-900">
                                        {{ $project->project_name }}
                                    </div>

                                    <div class="text-[10px] text-slate-400 mt-1">
                                        {{ strtoupper(str_replace('_', ' ', $project->project_type ?? 'PROJECT')) }}
                                    </div>
                                </td>

                                <td class="px-4 py-5 border-r border-slate-200">
                                    {{ $project->company?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-5 border-r border-slate-200">
                                    {{ $project->client?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-5 font-mono text-green-700 border-r border-slate-200">
                                    {{ number_format((float) $project->contract_amount, 2) }}
                                </td>

                                <td class="px-4 py-5 font-mono text-red-700 border-r border-slate-200">
                                    {{ number_format((float) $project->estimated_cost, 2) }}
                                </td>

                                <td class="px-4 py-5 border-r border-slate-200">
                                    <span class="px-2 py-1 text-[10px] font-bold uppercase border
                                        @if(in_array($project->status, ['approved','in_progress','completed','closed']))
                                            bg-green-50 text-green-700 border-green-300
                                        @elseif($project->status === 'cancelled')
                                            bg-red-50 text-red-700 border-red-300
                                        @elseif($project->status === 'draft')
                                            bg-amber-50 text-amber-700 border-amber-300
                                        @else
                                            bg-slate-50 text-slate-700 border-slate-300
                                        @endif">
                                        {{ strtoupper(str_replace('_', ' ', $project->status ?? 'draft')) }}
                                    </span>
                                </td>

                                <td class="px-4 py-5">
                                    @if(Route::has('projects.show'))
                                        <a href="{{ route('projects.show', $project) }}"
                                           class="px-2 py-1 bg-slate-100 border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-200">
                                            View
                                        </a>
                                    @endif
                                    @if(Route::has('projects.report'))
                                        <a href="{{ route('projects.report', $project) }}"
                                        target="_blank"
                                        class="ml-1 px-2 py-1 bg-purple-50 border border-purple-300 text-xs font-bold text-purple-700 hover:bg-purple-100">
                                            Report
                                        </a>
                                    @endif
                                    @if(Route::has('projects.project-centre'))
                                        <a href="{{ route('projects.project-centre',  ['projectId' => $project->id]) }}"
                                        class="ml-1 px-2 py-1 bg-blue-50 border border-blue-300 text-xs font-bold text-blue-700 hover:bg-blue-100">
                                            Manage
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-slate-400 font-bold">
                                    No projects found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>