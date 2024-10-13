<?php

namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\free_pt_requests;
use App\Models\Lead;
use App\Models\User;
use App\Models\Membership;
class FreePtRequestsController extends Controller

{

    public function free_pt(Request $request){
        $Lead = Lead::where('user_id' , $request->user()->id)->first();

        $latest_membership = Membership::where('member_id', $Lead->id)->latest()->first();

        $previous_request = free_pt_requests::where([
            ['user_id' , $request->user()->id],
            ['membership_id' , $latest_membership->id] 
        ])->first();
         
        if($previous_request ||  $latest_membership->assigned_coach_id != null){
            return response()->json([
                'message' => "You Request This PT Before",
                'status'  => false ,
            ],442);
        }
        else{
            return response()->json([
                'message' => "You Have a free BT Sessions",
                'status'  => true ,
            ],200);
        }
    }
     public function Request_free_pt(Request $request){

        $Lead = Lead::where('user_id' , $request->user()->id)->first();

        $latest_membership = Membership::where('member_id', $Lead->id)->latest()->first();

        $previous_request = free_pt_requests::where([
            ['user_id' , $request->user()->id],
            ['membership_id' , $latest_membership->id]
        ])->first();
         
        if($previous_request){
            return response()->json([
                'message' => "You Request This PT Before",
                'status'  => false ,
            ],442);
        }
        else
        {
            $request_pt = new free_pt_requests();
            $request_pt->user_id = $request->user()->id;
            $request_pt->membership_id = $latest_membership->id;
            $request_pt->save();

            return response()->json([
                'message'=> "success",
                'status' =>  true ,
            ],200);
        }
        
     
    }
}
