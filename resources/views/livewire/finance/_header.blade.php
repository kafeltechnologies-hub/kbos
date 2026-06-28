<div class="bg-slate-950 text-white p-5 border border-slate-800 mb-6">
    <p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">KBOS ERP • Finance Module</p>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-black">{{ $title ?? 'Finance Module' }}</h1>
            <p class="text-xs text-slate-300 mt-1">{{ $subtitle ?? 'Finance operations integrated with materials, projects, payments and receipts.' }}</p>
        </div>
        <div class="text-right">
            <p class="text-[10px] uppercase text-slate-400 font-bold">System Date</p>
            <p class="font-mono font-bold">{{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>
</div>
