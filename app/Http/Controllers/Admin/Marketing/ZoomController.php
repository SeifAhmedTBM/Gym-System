<?php

namespace App\Http\Controllers\Admin\Marketing;

use Carbon\Carbon;
use App\Models\Branch;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Jubaer\Zoom\Facades\Zoom;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\ZoomService;
use Illuminate\Support\Facades\Auth;

class ZoomController extends Controller
{
    public function index()
    {
        $meetings = Zoom::getAllMeeting()['data']['meetings'];

        return view('admin.zoom.index',compact('meetings'));
    }

    public function create()
    {
        $branches   = Branch::pluck('name','id');
        $roles      = Role::pluck('title','id');
        
        return view('admin.zoom.create',compact('branches','roles'));
    }

    public function store(Request $request)
    {
        $zoom = Zoom::createMeeting([
            "agenda"        => $request['agenda'],
            "topic"         => $request['topic'],
            "type"          => 2, // 1 => instant, 2 => scheduled, 3 => recurring with no fixed time, 8 => recurring with fixed time
            "duration"      => $request['duration'], // in minutes
            "timezone"      => "Africa/Cairo",
            "password"      => isset($request['password']) ? $request['password'] : NULL,
            "start_time"    => $request['date'].'T'.$request['start_time'].':00Z', // set your start time
            "pre_schedule"  => false,  // set true if you want to create a pre-scheduled meeting
            // "schedule_for" => 'set your schedule for profile email ', // set your schedule for
            "settings" => [
                'join_before_host'      => true, // if you want to join before host set true otherwise set false
                'host_video'            => isset($request['host_video']) ? true : false, // if you want to start video when host join set true otherwise set false
                'participant_video'     => isset($request['video']) ? true : false, // if you want to start video when participants join set true otherwise set false
                'mute_upon_entry'       => isset($request['mute']) ? true : false, // if you want to mute participants when they join the meeting set true otherwise set false
                'waiting_room'          => false, // if you want to use waiting room for participants set true otherwise set false
                'audio'                 => 'both', // values are 'both', 'telephony', 'voip'. default is both.
                'auto_recording'        => 'none', // values are 'none', 'local', 'cloud'. default is none.
                'approval_type'         => isset($request['approval']) ? 0 : 1,// 0 => Automatically Approve, 1 => Manually Approve, 2 => No Registration Required
            ],
        ]);

        $zoom_service = new ZoomService;
        if (isset($request['branch_id']) && !isset($request['role_id'])) 
        {
            $zoom_service->branch_only($request,$zoom);
            
        }elseif (isset($request['role_id']) && !isset($request['branch_id'])) 
        {
            $zoom_service->roles_only($request,$zoom);
        }elseif(isset($request['role_id']) && isset($request['branch_id']))
        {   
            $zoom_service->branch_roles($request,$zoom);
        }else{
            $zoom_service->none($request,$zoom);
        }

        $this->sent_successfully();
        return redirect()->route('admin.zoom.index');
    }

    public function show($meeting_id)
    {
        $meeting = Zoom::getMeeting($meeting_id)['data'];

        return view('admin.zoom.show',compact('meeting'));
    }

    public function destroy($meeting_id)
    {
        $meetings = Zoom::deleteMeeting($meeting_id);

        return back();
    }

    public function end_meeting($meeting_id)
    {
        $meeting = Zoom::endMeeting($meeting_id);
        
        $this->sent_successfully();
        return back();
    }
}
