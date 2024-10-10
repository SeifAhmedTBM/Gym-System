<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\Admin\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScheduleApiController extends Controller
{
    public function index()
{
    try {
       
        $schedules = Schedule::with(['session', 'timeslot', 'trainer'])->get();

 
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
}
