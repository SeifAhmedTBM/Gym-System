<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Models\FreezeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreFreezeRequestRequest;
use App\Http\Requests\UpdateFreezeRequestRequest;
use App\Http\Resources\Admin\FreezeRequestResource;
use App\Models\Membership;

class FreezeRequestApiController extends Controller
{
    public function index()
    {
        if (auth('sanctum')->id()) 
        {
            $member = Lead::whereType('member')
                        ->whereUserId(auth('sanctum')->user()->id)
                        ->first();

            $freeze_requests = FreezeRequest::with(['membership'])
                                        ->whereHas('membership',fn($q) => $q->whereMemberId($member->id))
                                        ->latest()
                                        ->get();

            return response()->json([
                'freeze_requests' => $freeze_requests
            ],200);
        }else{
            return response()->json([
                'message' => "Please Login First !"
            ],200);
        }
    }

    public function store(Request $request)
    {
        if (auth('sanctum')->id()) 
        {
            $member = Lead::whereType('member')->whereUserId(auth('sanctum')->id())->first();
    
            // $membership = Membership::whereMemberId($member->id)->latest()->first();
    
            $freezeRequest = FreezeRequest::create([
                'membership_id'         => $request['membership_id'],
                'freeze'                => $request->freeze,
                'start_date'            => $request->start_date,
                'end_date'              => date('Y-m-d', strtotime($request->start_date. ' + '.$request->freeze.' days')),
                'status'                => 'pending',
                'created_by_id'         => Auth('sanctum')->id(),
            ]);
    
            // return response()->json(['data' => $freezeRequest], 201);
            return response()->json(['message' => 'Created successfully'], 201);
        }else{
            return response()->json(['message'  => 'Please Login First !'],201);
        }
    }

    public function show(FreezeRequest $freezeRequest)
    {
        abort_if(Gate::denies('freeze_request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new FreezeRequestResource($freezeRequest->load(['membership', 'created_by']));
    }

    public function update(UpdateFreezeRequestRequest $request, FreezeRequest $freezeRequest)
    {
        $freezeRequest->update($request->all());

        return (new FreezeRequestResource($freezeRequest))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(FreezeRequest $freezeRequest)
    {
        abort_if(Gate::denies('freeze_request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $freezeRequest->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
