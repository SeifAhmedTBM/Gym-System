<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\Admin\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Membership;
use App\Models\SessionList;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use App\Models\Session_attendance;
class ScheduleApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            
            $user_id = $request->user()->id;
            $lead = Lead::where('user_id' , $request->user()->id)->first();
            $schedules = Schedule::where('branch_id' , $lead->branch_id)->with(['session', 'timeslot', 'trainer'])->get();

    
            return response()->json([
                'success' => "successfully",
                'data' => new ScheduleResource($schedules),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            
            \Log::error($e->getMessage());

        
            return response()->json([
                'success' => "Failed",
                'message' => 'Failed to retrieve schedules.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreScheduleRequest $request)
    {
        $schedule = Schedule::create($request->all());

        return (new ScheduleResource($schedule))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Schedule $schedule)
    {
        abort_if(Gate::denies('schedule_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ScheduleResource($schedule->load(['session', 'timeslot', 'trainer']));
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $schedule->update($request->all());

        return (new ScheduleResource($schedule))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Schedule $schedule)
    {
        abort_if(Gate::denies('schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schedule->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function attend_session(Request $request)
    {
      
        $user_id = $request->user()->id;
       
        $session_id = $request->session_id;

        $session = SessionList::where('id',$session_id)->first();
        $user_session_attendance = Session_attendance::where('user_id' ,$user_id )->where('session_id',$session_id)->first();
        
        $today_session_attendance = Session_attendance::where('created_at', '<=', Carbon::now()->endOfDay())
        ->count();
        
        if($today_session_attendance < $session->max_capacity && $user_session_attendance == null){
            $session_attendance = new Session_attendance();
            $session_attendance->user_id =  $user_id;
            $session_attendance->session_id = $session_id;
            $session_attendance->save();

                return response()->json([
                'status' =>  true ,
                'message' => "Success",
                'Rest In the Session' => $session->max_capacity - $today_session_attendance ,
                ], 200);
        }
        else{
            return response()->json([
                'status' =>  false ,
                'message' => "This Slot Reach Max Capacity Or You Attend Befor",
                'Rest In the Session' => $session->max_capacity - $today_session_attendance ,
                ], 442);
        
            }
    }
}
