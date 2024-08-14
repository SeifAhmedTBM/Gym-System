<?php

namespace App\Livewire;

use App\Models\Lead;
use Livewire\Component;

class TransferMemberComponent extends Component
{
    public $searchQuery = '';

    public function __construct()
    {
        $this->searchQuery = old('lead');
    }

    public function render()
    {
        $members = Lead::where('name', 'LIKE', '%'.$this->searchQuery.'%')->orWhere('phone', $this->searchQuery)->get();
        return view('livewire.transfer-member-component', [
            'members'       => $this->searchQuery != old('lead') ? $members : []
        ]);
    }
}
