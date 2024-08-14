<?php

namespace App\Http\Livewire;

use App\Models\Account;
use App\Models\AssetsMaintenance;
use Livewire\Component;

class GetAccountsComponent extends Component
{
    public $amount = '';
    public $assetsMaintenance;
    public function mount()
    {
        $this->amount = AssetsMaintenance::find($this->assetsMaintenance)->amount ?? '';
    }
    public function render()
    {
        $accounts = Account::where('balance', '>=', $this->amount)->pluck('name', 'id');
        return view('livewire.get-accounts-component',['accounts' => $accounts]);
    }
}
