<?php

namespace App\Livewire;

use App\Models\Lead;
use App\Models\Branch;
use Livewire\Component;

class SearchGlobalMember extends Component
{
    public $searchQuery = '';

    public function render()
    {
        if($this->searchQuery != NULL) 
        {
            $employee = Auth()->user()->employee;
            $prefixes = Branch::get()->pluck('member_prefix')->toArray();
            if(in_array(substr($this->searchQuery, 0, 2), $prefixes))
            {
                $code = substr($this->searchQuery, 2);
                
                $branch = Branch::where('member_prefix',substr($this->searchQuery, 0, 2))->first();
                if($branch){
                    $members = Lead::where('name', 'LIKE', '%' . $this->searchQuery . '%')
                    ->orWhere('phone', 'LIKE', '%' . $this->searchQuery . '%')->orWhere('member_code', $code)
                    ->with('branch')
                    ->where('branch_id',$branch->id)
                    ->limit(20)
                    ->get(['name', 'member_code', 'phone', 'id','branch_id']);
                }else{
                    $members = Lead::where('name', 'LIKE', '%' . $this->searchQuery . '%')
                    ->orWhere('phone', 'LIKE', '%' . $this->searchQuery . '%')->orWhere('member_code', $code)
                    ->with('branch')
                    ->limit(20)
                    ->get(['name', 'member_code', 'phone', 'id','branch_id']);
                }
               
            }else{
                $members = Lead::where('name', 'LIKE', '%' . $this->searchQuery . '%')
                ->orWhere('phone', 'LIKE', '%' . $this->searchQuery . '%')
                ->orWhere('member_code', $this->searchQuery)
                ->with('branch')
                ->limit(20)
                ->get(['name', 'member_code', 'phone', 'id','branch_id']);
            }
        }else {
            $members = collect([]);
        }
        
        return view('livewire.search-global-member', compact('members'));
    }
}
