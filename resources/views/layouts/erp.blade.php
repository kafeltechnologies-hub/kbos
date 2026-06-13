<!DOCTYPE html>
<html lang="en">
<head>
    <title>Kafel ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
</head>

<body class="bg-slate-100 text-slate-900">

<div class="flex min-h-screen">

    <aside class="w-72 bg-slate-950 text-white flex flex-col">
        <div class="p-6 border-b border-slate-800">
            <h1 class="text-2xl font-bold">Kafel ERP</h1>
            <p class="text-xs text-slate-400 mt-1">Business Operating System</p>
        </div>

        <nav class="flex-1 p-4 space-y-6 text-sm">

            <div>
                <p class="text-xs uppercase tracking-wider text-slate-500 mb-2">Main</p>
                <a href="/dashboard" class="block px-4 py-3 rounded-xl hover:bg-slate-800">
                    Dashboard
                </a>
            </div>

            <div>
                <p class="text-xs uppercase tracking-wider text-slate-500 mb-2">Organization</p>
                <a href="/organization/companies" class="block px-4 py-3 rounded-xl hover:bg-slate-800">
                    Companies
                </a>
                <a href="/organization/branches" class="block px-4 py-3 rounded-xl hover:bg-slate-800">
                    Branches
                </a>
                <a href="/organization/departments" class="block px-4 py-3 rounded-xl hover:bg-slate-800">
                    Departments
                </a>
            </div>

            <div>
                <p class="text-xs uppercase tracking-wider text-slate-500 mb-2">Operations</p>
                <a href="#" class="block px-4 py-3 rounded-xl hover:bg-slate-800">HR</a>
                <a href="#" class="block px-4 py-3 rounded-xl hover:bg-slate-800">Projects</a>
                <a href="#" class="block px-4 py-3 rounded-xl hover:bg-slate-800">Procurement</a>
                <a href="#" class="block px-4 py-3 rounded-xl hover:bg-slate-800">Inventory</a>
                <a href="{{ route('finance.dashboard') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800">Finance</a>
                <a href="#" class="block px-4 py-3 rounded-xl hover:bg-slate-800">Reports</a>
            </div>

        </nav>

        <div class="p-4 border-t border-slate-800 text-xs text-slate-400">
            SAP-style ERP Foundation
        </div>
    </aside>

    <div class="flex-1 flex flex-col">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
            <div>
                <h2 class="font-semibold">Kafel ERP Workspace</h2>
                <p class="text-xs text-slate-500">Engineering • Projects • Finance • Operations</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-medium">Admin User</p>
                    <p class="text-xs text-slate-500">System Administrator</p>
                </div>

                <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold">
                    KA
                </div>
            </div>
        </header>

        <main class="flex-1 p-8">
            {{ $slot }}
        </main>

    </div>

</div>

@livewireScripts

</body>
</html>