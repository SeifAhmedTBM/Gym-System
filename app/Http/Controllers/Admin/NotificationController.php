<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\FcmNotification;
class NotificationController extends Controller
{
    public function index(){
        $branches = Branch::get();
        return view('admin.Notifications.index' , compact('branches'));
    }


    public function sendNotification(Request $request){
        $users_array = []; 

        $title = 'Hello M.F';
        $body = $request->body;


        if($request->branch == null){
            $users = Lead::whereNotNull('branch_id')->whereNotNull('fcm_token')->get(); 
        }
        else{
            $users = Lead::where('branch_id' , $request->branch)->whereNotNull('fcm_token')->get();
        }
        
        foreach ($users as $user) {
            $user->notify(new FcmNotification($title, $body));
            // $user = User::find($lead->user_id); 
            // if ($user) { 
            //     $users_array[] = $user; 
            // }
        }
        return back()->with('success' , 'send succefully');
    }
}
