<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\Accounting\AccountingPostingService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class JournalEntriesPage extends Component
{
    public string $search = '';

    public ?int $editingJournalId = null;
    public bool $isEditing = false;

    public ?string $journal_date = null;
    public ?string $reference_no = null;
    public ?string $narration = null;

    public $total_debit = 0;
    public $total_credit = 0;
    public $difference = 0;

    public string $status = 'draft';

    public ?string $prepared_by = null;
    public ?string $approved_by = null;

    public array $lines = [];

    public array $statuses = [
        'draft',
        'prepared',
        'approved',
        'posted',
        'cancelled',
    ];

    public function mount(): void
    {
        $this->journal_date = now()->toDateString();

        $this->lines = [
            $this->blankLine(),
            $this->blankLine(),
        ];
    }

    public function blankLine(): array
    {
        return [
            'account_id' => null,
            'description' => '',
            'debit' => 0,
            'credit' => 0,
        ];
    }

    public function updatedLines(): void
    {
        $this->calculateTotals();
    }

    public function updatedLinesDebit(): void
    {
        $this->calculateTotals();
    }

    public function updatedLinesCredit(): void
    {
        $this->calculateTotals();
    }

    public function addLine(): void
    {
        $this->lines[] = $this->blankLine();

        $this->calculateTotals();
    }

    public function removeLine(int $index): void
    {
        unset($this->lines[$index]);

        $this->lines = array_values($this->lines);

        if (count($this->lines) < 2) {
            $this->lines[] = $this->blankLine();
        }

        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $debit = 0;
        $credit = 0;

        foreach ($this->lines as $line) {
            $debit += (float) ($line['debit'] ?? 0);
            $credit += (float) ($line['credit'] ?? 0);
        }

        $this->total_debit = round($debit, 2);
        $this->total_credit = round($credit, 2);
        $this->difference = round($this->total_debit - $this->total_credit, 2);
    }

    public function generateJournalNumber(): string
    {
        $last = JournalEntry::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'JV' . date('Y') . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function createNew(): void
    {
        $this->clearForm();

        session()->flash('info', 'New journal entry buffer initialized.');
    }

    public function clearBuffer(): void
    {
        $this->clearForm();

        session()->flash('info', 'Journal entry buffer cleared.');
    }

    public function sync(): void
    {
        $this->calculateTotals();

        session()->flash('info', 'Journal entries synchronized.');
    }

    public function save(): void
    {
        $this->calculateTotals();

        $this->validate([
            'journal_date' => ['required', 'date'],
            'narration' => ['required', 'string'],
            'status' => ['required', 'string'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:accounts,id'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (round((float) $this->total_debit, 2) <= 0) {
            $this->addError('total_debit', 'Total debit must be greater than zero.');
            return;
        }

        if (round((float) $this->total_debit, 2) !== round((float) $this->total_credit, 2)) {
            $this->addError('difference', 'Journal entry is not balanced. Debit and Credit must be equal.');
            return;
        }

        foreach ($this->lines as $index => $line) {
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);

            if ($debit > 0 && $credit > 0) {
                $this->addError("lines.$index.debit", 'A journal line cannot have both debit and credit.');
                return;
            }

            if ($debit <= 0 && $credit <= 0) {
                $this->addError("lines.$index.debit", 'Each line must have either debit or credit.');
                return;
            }
        }

        $payload = [
            'journal_date' => $this->journal_date,
            'reference_no' => $this->reference_no,
            'narration' => $this->narration,
            'total_debit' => $this->total_debit,
            'total_credit' => $this->total_credit,
            'status' => $this->status,
            'prepared_by' => $this->prepared_by,
            'approved_by' => $this->approved_by,
        ];

        $wasEditing = $this->isEditing;

        DB::transaction(function () use ($payload) {
            if ($this->isEditing && $this->editingJournalId) {
                $journal = JournalEntry::findOrFail($this->editingJournalId);
                $journal->update($payload);
                $journal->lines()->delete();
            } else {
                $journal = JournalEntry::create(array_merge($payload, [
                    'journal_number' => $this->generateJournalNumber(),
                ]));
            }

            foreach ($this->lines as $line) {
                $journal->lines()->create([
                    'account_id' => $line['account_id'],
                    'description' => $line['description'] ?? null,
                    'debit' => (float) ($line['debit'] ?? 0),
                    'credit' => (float) ($line['credit'] ?? 0),
                ]);
            }
        });

        $this->clearForm();

        session()->flash(
            'success',
            $wasEditing ? 'Journal entry updated successfully.' : 'Journal entry saved successfully.'
        );
    }

    public function postJournal(int $journalId): void
    {
        $journal = JournalEntry::with(['lines.account'])->findOrFail($journalId);

        if (round((float) $journal->total_debit, 2) !== round((float) $journal->total_credit, 2)) {
            session()->flash('error', 'Journal entry cannot be posted because it is not balanced.');
            return;
        }

        $journal->update([
            'status' => 'posted',
        ]);

        app(AccountingPostingService::class)->postJournal($journal);

        session()->flash('success', 'Journal entry posted to General Ledger successfully.');
    }

    public function approveJournal(int $journalId): void
    {
        JournalEntry::findOrFail($journalId)->update([
            'status' => 'approved',
            'approved_by' => $this->approved_by ?: 'System Approver',
        ]);

        session()->flash('success', 'Journal entry approved successfully.');
    }

    public function cancelJournal(int $journalId): void
    {
        JournalEntry::findOrFail($journalId)->update([
            'status' => 'cancelled',
        ]);

        session()->flash('info', 'Journal entry cancelled.');
    }

    public function editJournal(int $journalId): void
    {
        $journal = JournalEntry::with('lines')->findOrFail($journalId);

        $this->editingJournalId = $journal->id;
        $this->isEditing = true;

        $this->journal_date = $journal->journal_date;
        $this->reference_no = $journal->reference_no;
        $this->narration = $journal->narration;
        $this->total_debit = $journal->total_debit;
        $this->total_credit = $journal->total_credit;
        $this->difference = round((float) $journal->total_debit - (float) $journal->total_credit, 2);
        $this->status = $journal->status;
        $this->prepared_by = $journal->prepared_by;
        $this->approved_by = $journal->approved_by;

        $this->lines = $journal->lines
            ->map(fn ($line) => [
                'account_id' => $line->account_id,
                'description' => $line->description,
                'debit' => $line->debit,
                'credit' => $line->credit,
            ])
            ->toArray();

        if (count($this->lines) < 2) {
            $this->lines[] = $this->blankLine();
        }

        session()->flash('info', 'Journal entry loaded for editing.');
    }

    public function clearForm(): void
    {
        $this->reset([
            'editingJournalId',
            'isEditing',
            'journal_date',
            'reference_no',
            'narration',
            'total_debit',
            'total_credit',
            'difference',
            'status',
            'prepared_by',
            'approved_by',
            'lines',
        ]);

        $this->journal_date = now()->toDateString();
        $this->status = 'draft';
        $this->total_debit = 0;
        $this->total_credit = 0;
        $this->difference = 0;

        $this->lines = [
            $this->blankLine(),
            $this->blankLine(),
        ];
    }

    public function render()
    {
        $accounts = Account::where('active', true)
            ->orderBy('account_code')
            ->get();

        $journals = JournalEntry::with('lines.account')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('journal_number', 'like', "%{$this->search}%")
                        ->orWhere('reference_no', 'like', "%{$this->search}%")
                        ->orWhere('narration', 'like', "%{$this->search}%")
                        ->orWhere('status', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->get();

        return view('livewire.finance.journal-entries-page', compact(
            'accounts',
            'journals'
        ))->layout('layouts.erp');
    }
}