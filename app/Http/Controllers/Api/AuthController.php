<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use App\Models\User;
use App\Models\Setting;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // return 1;
        $validatedData = Validator::make($request->all(), [
            'phone'         => 'required|exists:leads,phone',
            'member_code'   => 'required|exists:leads,member_code'
        ]);

        if($validatedData->fails()) {
            $errors = [];
            return response()->json($validatedData->errors()->all(), 422); 
        }
        $lead = Lead::with(['user'])
                        ->where('phone', $request['phone'])
                        ->where('member_code', $request['member_code'])
                        ->first();

        if (!$lead) 
        {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 422);
        }

        // if($lead->user)
        // {
            $authToken = $lead->user;
            
            $authToken = $lead->user->createToken('auth-token')->plainTextToken;
        // }else{
        //     return response()->json([
        //         'message'       => 'something went wrong !'
        //     ]);
        // }


        $data['user'] = $lead->user;
        $data['access_token'] = $authToken;
        
        return response()->json($data, 201);
    }


    public function logout() 
    {
        if (auth('sanctum')->id()) 
        {
            User::find(auth('sanctum')->id())->tokens()->delete();

            return response()->json([
                'message' => 'Logged Out Successfully'
            ], 201);
        }else{
            return response()->json([
                'message' => "You Didn't Login Before, Please Login First !!"
            ], 201);
        }
    }

    public function getMemberships()
    {
        if (auth('sanctum')->id()) 
        {
            $member = Lead::whereUserId(Auth('sanctum')->id())
                            ->with([
                                'memberships'
                            ])->first();

            return response()->json([
                'memberships'   => $member->memberships
            ], 201);
        }else{
            return response()->json([
                'message'       => 'Please Login First !'
            ], 201);
        }
        
        
    }

    public function profile()
    {
        if(auth('sanctum')->id())
        {
            $member = Lead::with(['user','status','address','source'])
                                ->whereUserId(auth('sanctum')->id())
                                ->first();
        }else{
            return response()->json([
                'Please Login first !'
            ],404);
        }

        if($member)
        {
            $memberships = Membership::with(['service_pricelist','sales_by','trainer'])
                            ->withCount('attendances')
                            ->withSum('freezeRequests','freeze')
                            ->whereMemberId($member->id)
                            ->get();
    
            return response()->json([
                'member'        => $member,
                'memberships'   => $memberships,
            ],200);
        }else{
            return response()->json([
                'Something went wrong'
            ],404);
        }
    }


    public function sendOtp(Request $request)
    {
        if($request['field'] == '0000'){
            return response()->json([
                'message' => 'Success'
            ], 200);
        }else{
            return response()->json([
                'message' => 'Invalid OTP'
            ], 422);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(auth('sanctum')->id());

        $user->update([
            'name'              =>  $request['name']
        ]);

        return response()->json(['message' => 'Updated successfully'], 201);
    }

    public function updateImage(Request $request)
    {
        if (auth('sanctum')->id()) 
        {
            $member = Lead::whereType('member')
                                ->whereUserId(auth('sanctum')->user()->id)
                                ->first();
    
            if ($request->file('photo')) 
            {
                if ($member->photo) {
                    $member->photo->delete();
                }
                $member->addMediaFromRequest('photo')->toMediaCollection('photo');
            }
    
            return response()->json(['message' => 'Updated successfully'], 201);
        }else{
            return response()->json(['message' => 'Please Login First !'], 201);
        }
    }

    public function trainers()
    {
        if (auth('sanctum')->id()) {
            $member = Lead::with(['status','address','source'])->whereUserId(auth('sanctum')->user()->id)->first();
    
            $memberships = Membership::with([
                    'service_pricelist',
                    'sales_by',
                    'trainer'           => fn($q) => ($q->withSum('ratings','rate') && $q->withCount('ratings')),
                    'assigned_coach'    => fn($q) => ($q->withSum('ratings','rate') && $q->withCount('ratings')),
                ])
                ->withCount(['attendances'])
                ->withSum('freezeRequests','freeze')
                ->whereMemberId($member->id)
                ->get()
                ->pluck('trainer')
                ->unique('trainer');
    
            $trainer_average = User::withSum('ratings','rate')->withCount('ratings')->whereIn('id',$memberships)->get();
    
            return response()->json([
                'trainer' => $trainer_average
            ],200);
        }else{
            return response()->json([
                'message'       => 'Please Login first !'
            ],200);
        }
    }

    public function currentTrainer()
    {
        if (auth('sanctum')->id()) 
        {
            $member = Lead::with(['status','address','source'])->whereUserId(auth('sanctum')->user()->id)->first();
            
            $membership = Membership::whereMemberId($member->id)
                            ->with([
                                'trainer' => fn($q) => ($q->withSum('ratings','rate') && $q->withCount('ratings'))
                            ])
                            ->latest()
                            ->firstOr(function(){
                                    return response()->json(['message' => 'Current membership is expired']
                            );
            });
    
            return response()->json([
                'trainer' => $membership->trainer
            ],200);
        }else{
            return response()->json([
                'message' => 'Please Login First !'
            ],200);
        }
    }

    public function contact()
    {
        $settings = Setting::first()
                ->makeHidden(['id','invoice','has_lockers','freeze_duration','payroll_day','max_discount','freeze_request','inactive_members_days','invoice_prefix','member_prefix','login_background','color']);

        return response()->json([
            'settings' => $settings
        ]);
    }

    public function privacy()
    {
        $privacy = Setting::first()->privacy;

        return response()->json([
            'privacy' => $privacy ?? ''
        ]);
    }

    public function terms()
    {
        $terms = Setting::first()->terms;

        return response()->json([
            'terms' => $terms ?? ''
        ]);
    }
}
