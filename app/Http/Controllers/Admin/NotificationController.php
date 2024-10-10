<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Lead;
use App\Models\User;
class NotificationController extends Controller
{
    public function index(){
        $branches = Branch::get();
        return view('admin.Notifications.index' , compact('branches'));
    }


    public function sendNotification(Request $request){
        $users_array = []; 

        if($request->branch == null){
            $users = Lead::whereNotNull('branch_id')->get(); 
        }
        else{
            $users = Lead::where('branch_id' , $request->branch)->get();
        }
        
        foreach ($users as $lead) {
            $user = User::find($lead->user_id); 
            if ($user) { 
                $users_array[] = $user; 
            }
        }
        dd($users_array);
    }
}
