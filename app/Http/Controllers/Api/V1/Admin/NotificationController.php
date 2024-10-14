<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Models\MemberSuggestion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Models\user_notifications;


class NotificationController extends Controller
{


    public function index(Request $request){

        $user_id = $request->user()->id;

        $notifications = user_notifications::where('user_id',$user_id)->with('notification')->get();

        return response()->json([
            'status'                  => true ,
            'notifications'                    =>  $notifications      
        ],200);
    }

}