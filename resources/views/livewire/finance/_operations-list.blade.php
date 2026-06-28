<div class="divide-y divide-slate-200">
    @forelse($items ?? [] as $item)
        @php
            $docType = $type ?: ($item->source_type ?? null);
            $amount = max((float)($item->debit_total ?? 0), (float)($item->credit_total ?? 0), (float)($item->debit ?? 0), (float)($item->credit ?? 0));
            $printRoute = match($docType) {
                'quotation' => 'finance.operations.quotation.print',
                'invoice' => 'finance.operations.invoice.print',
                'receipt' => 'finance.operations.receipt.print',
                'payment' => 'finance.operations.payment.print',
                default => null,
            };
        @endphp

        <div class="p-4 flex flex-col xl:flex-row xl:justify-between gap-4">
            <div>
                <div class="flex flex-wrap gap-2 items-center">
                    <p class="font-mono font-black text-green-700">{{ $item->reference ?? '-' }}</p>
                    <span class="px-2 py-1 text-[10px] uppercase font-bold border bg-slate-50 border-slate-300">
                        {{ $docType ?? '-' }}
                    </span>
                    <span class="px-2 py-1 text-[10px] uppercase font-bold border bg-blue-50 text-blue-700 border-blue-300">
                        {{ $item->status ?? 'posted' }}
                    </span>
                </div>

                <p class="text-xs font-bold mt-1">{{ $item->narration ?? '-' }}</p>
                <p class="text-[10px] text-slate-500 mt-1">
                    {{ isset($item->entry_date) ? \Carbon\Carbon::parse($item->entry_date)->format('d M Y') : '-' }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2 items-start">
                <div class="bg-slate-50 border border-slate-200 px-3 py-2 min-w-32">
                    <p class="text-[10px] uppercase font-bold text-slate-500">Amount</p>
                    <p class="font-mono font-black">{{ number_format($amount, 2) }}</p>
                </div>

                <button type="button"
                        wire:click="approveTransaction('{{ $item->reference }}')"
                        class="bg-green-50 text-green-700 border border-green-300 px-3 py-2 text-[10px] font-bold">
                    Approve
                </button>

                <button type="button"
                        wire:click="editTransaction('{{ $item->reference }}', '{{ $docType }}')"
                        class="bg-blue-50 text-blue-700 border border-blue-300 px-3 py-2 text-[10px] font-bold">
                    Edit
                </button>

                <button type="button"
                        wire:click="reverseTransaction('{{ $item->reference }}')"
                        onclick="return confirm('Reverse this transaction?')"
                        class="bg-amber-50 text-amber-700 border border-amber-300 px-3 py-2 text-[10px] font-bold">
                    Reverse
                </button>

                <button type="button"
                        wire:click="deleteTransaction('{{ $item->reference }}')"
                        onclick="return confirm('Delete this transaction permanently?')"
                        class="bg-red-50 text-red-700 border border-red-300 px-3 py-2 text-[10px] font-bold">
                    Delete
                </button>

                @if($printRoute && Route::has($printRoute))
                    <a target="_blank"
                       href="{{ route($printRoute, $item->reference) }}"
                       class="bg-slate-900 text-white border border-slate-950 px-3 py-2 text-[10px] font-bold">
                        Print
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="p-10 text-center text-slate-400 font-bold">
            No transactions found.
        </div>
    @endforelse
</div>