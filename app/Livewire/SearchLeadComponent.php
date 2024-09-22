<?php

namespace App\Livewire;

use App\Models\Lead;
use Livewire\Component;

class SearchLeadComponent extends Component
{
    public $lead_name = NULL;

    public function render()
    {
        if($this->lead_name != NULL) {
            $leads = Lead::where('name', 'LIKE', '%' . $this->lead_name . '%')
                ->orWhere('phone', 'LIKE', '%' . $this->lead_name . '%')
                ->limit(10)
                ->get();
        }else {
            $leads = [];
        }
        return view('livewire.search-lead-component', compact('leads'));
    }
}
