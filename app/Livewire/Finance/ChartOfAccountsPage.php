<?php

namespace App\Livewire\Finance;

use App\Models\ChartOfAccount;
use Livewire\Component;
use Livewire\WithPagination;

class ChartOfAccountsPage extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $account_code = '';
    public string $account_name = '';
    public string $account_type = '';
    public string $category = '';
    public string $description = '';
    public bool $active = true;

    protected function rules(): array
    {
        return [
            'account_code' => 'required|max:50',
            'account_name' => 'required|max:255',
            'account_type' => 'required|max:100',
            'category'     => 'nullable|max:100',
            'description'  => 'nullable|max:1000',
            'active'    => 'boolean',
        ];
    }

    public function resetForm(): void
    {
        $this->editingId = null;

        $this->account_code = '';
        $this->account_name = '';
        $this->account_type = '';
        $this->category = '';
        $this->description = '';
        $this->active = true;
    }

    public function save(): void
    {
        $this->validate();

        ChartOfAccount::updateOrCreate(
            ['id' => $this->editingId],
            [
                'account_code' => $this->account_code,
                'account_name' => $this->account_name,
                'account_type' => $this->account_type,
                'category'     => $this->category,
                'description'  => $this->description,
                'active'    => $this->active,
            ]
        );

        session()->flash('success', 'Account saved successfully.');

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $account = ChartOfAccount::findOrFail($id);

        $this->editingId = $account->id;
        $this->account_code = $account->account_code;
        $this->account_name = $account->account_name;
        $this->account_type = $account->account_type;
        $this->description = $account->description ?? '';
        $this->active = (bool) $account->active;
    }

    public function delete(int $id): void
    {
        ChartOfAccount::findOrFail($id)->delete();

        session()->flash('success', 'Account deleted successfully.');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
        {
            $accounts = ChartOfAccount::query()
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('account_code', 'ilike', "%{$this->search}%")
                            ->orWhere('account_name', 'ilike', "%{$this->search}%")
                            ->orWhere('account_type', 'ilike', "%{$this->search}%")
                            ->orWhere('category', 'ilike', "%{$this->search}%");
                    });
                })
                ->orderBy('account_code')
                ->paginate(20);

            return view('livewire.finance.chart-of-accounts-page', [
                'accounts' => $accounts,
            ]);
        }
}