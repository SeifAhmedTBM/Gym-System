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
                'message'=>"success",
                'data' =>[
                    'freeze'=> $freeze_requests
                ]
            ],200);
        }else{
            return response()->json([
                'message' => "Please Login First !",
                'data'=>null
            ],403);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'membership_id'    => 'required|exists:memberships,id', // Ensure the membership ID exists in the memberships table
                'number_of_days'   => 'required|integer|min:1', // Validate freeze is a positive integer
                'start_date'       => 'required|date|after:today', // Start date must be a valid future date
            ]);
            
            $user_id = $request->user()->id;

            if ($user_id) {
                $member = Lead::whereType('member')->whereUserId($user_id)->first();
                // Check if a freeze request for the same membership is already pending
                $existingFreezeRequest = FreezeRequest::where('membership_id', $validated['membership_id'])
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->first();

                if ($existingFreezeRequest) {
                    return response()->json([
                        'message' => 'A freeze request for this membership is already pending or confirmed.',
                        'data' => null
                    ], 422); // Conflict response
                }
                else{
                    $freezeRequest = FreezeRequest::create([
                        'membership_id'     => $validated['membership_id'],
                        'freeze'            => $validated['number_of_days'],
                        'start_date'        => $validated['start_date'],
                        'end_date'          => date('Y-m-d', strtotime($validated['start_date']. ' + '.$validated['number_of_days'].' days')),
                        'status'            => 'pending',
                        'created_by_id'     => $request->user()->id,
                    ]);

                    return response()->json(['message' => 'Created successfully','data'=>$freezeRequest], 200);
                }
               
            } else {
                return response()->json([
                    'message' => "Please Login First!",
                    'data' => null
                ], 403);
            }
        } 
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Failed',
                'data'=>[

                'errors' => $e->errors() // Return detailed validation errors
                ]
            ], 422);
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
