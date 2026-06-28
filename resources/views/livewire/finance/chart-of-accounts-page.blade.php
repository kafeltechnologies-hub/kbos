<div class="min-h-screen bg-slate-100 p-6 text-slate-900">
<div class="bg-slate-950 text-white p-4 border border-slate-800 mb-6"><p class="text-[10px] uppercase tracking-widest text-green-300 font-bold">Finance Module</p><h1 class="text-lg font-black">Chart of Accounts</h1></div>
@if(session()->has('success'))<div class="mb-4 bg-green-50 border-l-4 border-green-600 p-3 text-xs text-green-900">{{ session('success') }}</div>@endif
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
<div class="bg-white border border-slate-300 p-5">
<form wire:submit.prevent="save" class="space-y-3">
<input wire:model="account_code" placeholder="Account Code" class="w-full border px-3 py-2 text-xs">
<input wire:model="account_name" placeholder="Account Name" class="w-full border px-3 py-2 text-xs">
<select wire:model="account_type" class="w-full border px-3 py-2 text-xs">@foreach($accountTypes as $type)<option value="{{ $type }}">{{ strtoupper(str_replace('_',' ', $type)) }}</option>@endforeach</select>
<select wire:model="normal_balance" class="w-full border px-3 py-2 text-xs"><option value="debit">DEBIT</option><option value="credit">CREDIT</option></select>
<textarea wire:model="description" rows="3" placeholder="Description" class="w-full border px-3 py-2 text-xs"></textarea>
<label class="flex gap-2 items-center text-xs font-bold"><input type="checkbox" wire:model="active"> Active</label>
<div class="flex flex-wrap gap-2"><button class="bg-green-700 text-white px-4 py-2 text-xs font-bold">Save</button><button type="button" wire:click="clearForm" class="bg-white border px-4 py-2 text-xs font-bold">Clear</button><button type="button" wire:click="seedMaterialAccounts" class="bg-slate-800 text-white px-4 py-2 text-xs font-bold">Seed Material Accounts</button></div>
</form>
</div>
<div class="xl:col-span-2 bg-white border border-slate-300">
<div class="p-3 bg-slate-100 border-b"><input wire:model.live.debounce.500ms="search" placeholder="Search accounts..." class="w-full border px-3 py-2 text-xs"></div>
<table class="w-full text-xs"><thead class="bg-slate-100"><tr><th class="p-3 text-left">Code</th><th class="p-3 text-left">Name</th><th class="p-3 text-left">Type</th><th class="p-3 text-left">Normal</th><th class="p-3 text-left">Action</th></tr></thead>
<tbody class="divide-y">@forelse($accounts as $account)<tr><td class="p-3 font-mono">{{ $account->account_code }}</td><td class="p-3 font-bold">{{ $account->account_name }}</td><td class="p-3">{{ strtoupper(str_replace('_',' ', $account->account_type)) }}</td><td class="p-3">{{ strtoupper($account->normal_balance) }}</td><td class="p-3"><button wire:click="edit({{ $account->id }})" class="text-blue-700 font-bold">Edit</button></td></tr>@empty<tr><td colspan="5" class="p-8 text-center text-slate-400 font-bold">No accounts found.</td></tr>@endforelse</tbody></table>
</div>
</div>
</div>
