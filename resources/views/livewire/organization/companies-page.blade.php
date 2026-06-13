<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/geist@1.3.0/dist/font/sans.css">

<style>
    .font-erp-clean {
        font-family: 'Geist Sans', system-ui, -apple-system, sans-serif;
    }
</style>

<div class="min-h-screen bg-slate-100 text-slate-900 font-erp-clean p-6">

    <form id="company-form" >

        <div class="border border-slate-300 bg-white shadow-sm rounded-none overflow-hidden">

            <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-4 py-3 flex items-center justify-between border-b border-slate-900">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase font-mono block">
                        Organization Area
                    </span>

                    <h1 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0V11m0 0h4m-4 0H5m12 0h2" />
                        </svg>

                        Master Data — Corporate Registries
                    </h1>
                </div>

                <div class="flex items-center gap-6 text-right">
                    <div class="hidden sm:block border-l border-slate-600 pl-4">
                        <span class="text-[10px] block uppercase font-mono text-slate-400">
                            Ledger Count
                        </span>

                        <span class="text-base font-black font-mono text-white">
                            {{ count($companies) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">

                        <a href="{{ route('organization.companies.create') }}"
                        class="btn-primary">
                            Create New
                        </a>

                        <button type="submit"
                            formaction="{{ route('organization.companies.store') }}"
                            formmethod="POST"
                            class="btn-success">
                            Post Ledger
                        </button>

                        <a href="{{ route('organization.companies') }}"
                        class="btn-secondary">
                            Clear Buffer
                        </a>

                        <a href="{{ route('organization.companies.sync') }}"
                        class="btn-secondary">
                            Sync
                        </a>

                    </div>

                <div class="ml-auto px-3 py-1 bg-slate-200 border border-slate-300 text-[11px] font-mono font-bold text-slate-700 rounded-none">
                    SYSTEM GENERATED ID:
                    <span class="text-blue-700">
                        {{ $this->generateCompanyCode() }}
                    </span>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mt-4 border-l-4 border-green-600 bg-green-50 p-3 text-xs font-medium text-green-900 flex items-center gap-2 rounded-none shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('info'))
            <div class="mt-4 border-l-4 border-blue-600 bg-blue-50 p-3 text-xs font-medium text-blue-900 flex items-center gap-2 rounded-none shadow-sm">
                {{ session('info') }}
            </div>
        @endif

        <div class="mt-6 border border-slate-300 bg-white rounded-none shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                    01. General Profile Block
                </h2>
            </div>

            <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Company Legal Name <span class="text-red-500">*</span>
                    </label>

                    <div class="flex-1 min-w-0">
                        <input wire:model="name" type="text"
                            class="w-full text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">

                        @error('name')
                            <p class="mt-1 text-[11px] font-semibold text-red-600 whitespace-nowrap">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Country Zone
                    </label>

                    <select wire:model="country"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                        @foreach($countries as $countryName)
                            <option value="{{ $countryName }}">
                                {{ $countryName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Registration ID / No.
                    </label>

                    <input wire:model="registration_number" type="text"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                </div>

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Record State Status
                    </label>

                    <select wire:model="active"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                        <option value="1">Active Operational Ledger</option>
                        <option value="0">Inactive / Suspended</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="mt-8 border border-slate-300 bg-white rounded-none shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                    02. Statutory & Revenue Authority Ledger
                </h2>
            </div>

            <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Tax Identification (TIN)
                    </label>

                    <input wire:model="tin" type="text"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                </div>

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        VAT Registration Value
                    </label>

                    <input wire:model="vat_number" type="text"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                </div>

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        SSNIT Identifier
                    </label>

                    <input wire:model="ssnit_number" type="text"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                </div>

            </div>
        </div>

        <div class="mt-8 border border-slate-300 bg-white rounded-none shadow-sm overflow-hidden">
            <div class="bg-slate-100 px-3 py-1.5 border-b border-slate-200">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">
                    03. Communications & Address Node
                </h2>
            </div>

            <div class="p-6 bg-slate-50/20 grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-5">

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Primary Contact Email
                    </label>

                    <div class="flex-1 min-w-0">
                        <input wire:model="email" type="email"
                            class="w-full text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">

                        @error('email')
                            <p class="mt-1 text-[11px] font-semibold text-red-600 whitespace-nowrap">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center w-full">
                    <label class="w-44 shrink-0 text-right pr-5 text-xs font-bold text-slate-600">
                        Primary Office Phone
                    </label>

                    <input wire:model="phone" type="text"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition">
                </div>

                <div class="flex items-start w-full xl:col-span-2">
                    <label class="w-44 shrink-0 text-right pr-5 pt-1.5 text-xs font-bold text-slate-600">
                        Registered Address
                    </label>

                    <textarea wire:model="address" rows="2"
                        class="flex-1 min-w-0 text-xs bg-slate-50 border border-slate-300 rounded-none px-2.5 py-1.5 shadow-inner focus:bg-white focus:border-blue-600 focus:ring-0 outline-none transition resize-none"></textarea>
                </div>

            </div>
        </div>

    </form>

    <div class="mt-8 border border-slate-300 bg-white shadow-sm rounded-none overflow-hidden">

        <div class="bg-slate-800 px-4 py-3 border-b border-slate-900">
            <h2 class="text-xs font-bold uppercase tracking-wider text-white">
                Database Registry Ledger Outputs
            </h2>
        </div>

        <div class="w-full bg-slate-200 border-b border-slate-300 flex items-center shadow-inner">
            <span class="pl-4 text-slate-500 font-mono text-sm select-none">
                🔍
            </span>

            <input wire:model.live="search" type="text"
                placeholder="Filter records inline..."
                class="w-full bg-transparent border-0 px-3 py-3 text-xs text-slate-900 placeholder-slate-500 focus:ring-0 outline-none font-medium">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap table-fixed">

                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300 select-none">
                    <tr>
                        <th class="w-24 px-4 py-4 border-r border-slate-200">System Code</th>
                        <th class="w-56 px-4 py-4 border-r border-slate-200">Corporate Designation Name</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Tax TIN</th>
                        <th class="w-36 px-4 py-4 border-r border-slate-200">Telephone Line</th>
                        <th class="w-32 px-4 py-4 border-r border-slate-200">Country Origin</th>
                        <th class="w-24 px-4 py-4">State</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">

                    @forelse($companies as $company)
                        <tr class="hover:bg-blue-50/70 border-b border-slate-200 transition">
                            <td class="px-4 py-6 font-mono font-bold text-blue-800 border-r border-slate-200 bg-slate-50/50">
                                {{ $company['code'] ?? '-' }}
                            </td>

                            <td class="px-4 py-6 border-r border-slate-200 overflow-hidden text-ellipsis">
                                <div class="font-bold text-slate-900 truncate">
                                    {{ $company['name'] ?? '-' }}
                                </div>

                                <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5">
                                    {{ $company['email'] ?: 'NULL_PTR' }}
                                </div>
                            </td>

                            <td class="px-4 py-6 text-slate-600 font-mono border-r border-slate-200">
                                {{ $company['tin'] ?: '-' }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 border-r border-slate-200">
                                {{ $company['phone'] ?: '-' }}
                            </td>

                            <td class="px-4 py-6 text-slate-600 border-r border-slate-200">
                                {{ $company['country'] ?? '-' }}
                            </td>

                            <td class="px-4 py-6">
                                @if($company['active'] ?? false)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-none text-[10px] font-bold uppercase tracking-wide bg-green-100 text-green-800 border border-green-300">
                                        ACTIVE
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-none text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-500 border border-slate-300">
                                        LOCKED
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400 font-bold bg-slate-50/30 font-mono uppercase tracking-wider">
                                [Err] 0 records returned based on query arguments.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
</div>