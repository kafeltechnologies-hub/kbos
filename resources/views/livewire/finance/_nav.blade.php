@php
    $financeNavLinks = $financeNavLinks ?? [];
@endphp
<div class="bg-white border border-slate-300 mb-6 overflow-hidden">
    <div class="bg-slate-900 px-4 py-3"><p class="text-[10px] text-green-300 font-bold uppercase tracking-widest">Finance Navigation</p></div>
    <div class="flex flex-wrap gap-2 p-3 bg-slate-100">
        @foreach($financeNavLinks as $link)
            @if(Route::has($link['route']))
                <a href="{{ route($link['route']) }}" class="px-3 py-2 text-xs font-bold border {{ request()->routeIs($link['route']) ? 'bg-green-700 text-white border-green-800' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">{{ $link['label'] }}</a>
            @endif
        @endforeach
    </div>
</div>
