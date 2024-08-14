<?php

namespace App\Livewire;

use App\Models\Lead;
use Livewire\Component;

class SearchMember extends Component
{
    public $search = '';

    public $cities = [
        'Rajkot',
        'Surat',
        'Baroda',
    ]; 

    public function render()
    {
        return view('livewire.search-member',[
            'members' => Lead::whereType('member')->whereMemberCode($this->search)->get()->toArray(),
        ]);
    }
}
