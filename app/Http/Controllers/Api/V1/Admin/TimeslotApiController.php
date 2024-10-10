<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimeslotRequest;
use App\Http\Requests\UpdateTimeslotRequest;
use App\Http\Resources\Admin\TimeslotResource;
use App\Models\Timeslot;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TimeslotApiController extends Controller
{
    public function index()
    {
        
        try {
           
            $timeslots = Timeslot::all();
    
            return response()->json([
                'success' => "successfully",
                'data' => new TimeslotResource($timeslots),
            ], Response::HTTP_OK);
    
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
    
            return response()->json([
                'success' => "Failed",
                'message' => 'Failed to retrieve timeslots.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreTimeslotRequest $request)
    {
        $timeslot = Timeslot::create($request->all());

        return (new TimeslotResource($timeslot))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Timeslot $timeslot)
    {
        abort_if(Gate::denies('timeslot_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TimeslotResource($timeslot);
    }

    public function update(UpdateTimeslotRequest $request, Timeslot $timeslot)
    {
        $timeslot->update($request->all());

        return (new TimeslotResource($timeslot))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Timeslot $timeslot)
    {
        abort_if(Gate::denies('timeslot_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $timeslot->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
