<div class="min-h-screen bg-slate-100 p-6 text-slate-900">
@include('livewire.finance._header', ['title'=>'Finance Centre','subtitle'=>'One-stop finance command centre for KBOS ERP.'])
@include('livewire.finance._nav')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white border border-slate-300 p-5"><p class="text-[10px] uppercase font-bold text-slate-500">Pending Material Approvals</p><p class="text-4xl font-black font-mono text-red-700">{{ $pendingMaterials }}</p></div>
    <div class="bg-white border border-slate-300 p-5"><p class="text-[10px] uppercase font-bold text-slate-500">Posted GL Entries</p><p class="text-4xl font-black font-mono">{{ $postedGlEntries }}</p></div>
    <div class="bg-white border border-slate-300 p-5"><p class="text-[10px] uppercase font-bold text-slate-500">Recent Material Entries</p><p class="text-4xl font-black font-mono text-green-700">{{ $materialPostings->count() }}</p></div>
</div>
<div class="bg-white border border-slate-300">
    <div class="bg-slate-800 text-white p-4"><h2 class="text-xs font-bold uppercase">Finance Centre Menu</h2></div>
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6">
        @foreach($financeNavLinks as $link)
            @if(Route::has($link['route']))
                <a href="{{ route($link['route']) }}" class="p-4 border hover:bg-slate-50"><p class="font-black text-xs uppercase">{{ $link['label'] }}</p><p class="text-[10px] text-slate-500 mt-1">Open {{ $link['label'] }}</p></a>
            @endif
        @endforeach
    </div>
</div>
</div>
