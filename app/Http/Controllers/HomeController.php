<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Setting;
use App\Models\Status;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function privacy()
    {
        $privacy = Setting::first();
        return view('privacy',compact('privacy'));
    }

    public function terms()
    {
        $terms = Setting::first();
        return view('terms',compact('terms'));
    }

    public function duplicates()
    {
        $leads = Lead::with(['branch'])->orderBy('phone')->get();
        
        $leadsUnique = $leads->unique('phone');
        $duplicates = $leads->diff($leadsUnique);
        
        $collection = collect($duplicates);
        
        $counter = 0;
        foreach ($collection as $lead) 
        {
            $counter += 1;

            echo '<a href="'.route('admin.members.show',$lead->id).'">'
            .$lead->id.' - '.$lead->name.' - '.$lead->phone.' - '.($lead->branch->name ?? '-').' - '.$lead->type.
            '</a><br>';

            $lead->update([
                'status_id'     => Status::firstOrCreate([
                        'name'                      => 'Duplicate',
                        'color'                     => 'warning',
                        'default_next_followup_days'=> 0,
                        'need_followup'             => 'no'
                ])->id
            ]);
            // echo $lead->id.' - '.$lead->name.' - '.$lead->phone.' - '.($lead->branch->name ?? '-').' - '.$lead->type.'<br>';
        }
        return $counter.'<br><br><br><br><br><br><br><br><br>';
    }
}
