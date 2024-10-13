<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use App\Models\User;
use App\Models\Setting;
use App\Models\Membership;
use App\Models\MobileSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $setting;
    public function __construct(){
        $this->setting = MobileSetting::all()->first();
    }
    public function login(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'phone'         => 'required|exists:leads,phone',
            'member_code'   => 'required|exists:leads,member_code'
        ]);
        if($validatedData->fails()) {
            return response()->json([
                'message' => 'Invalid credentials',
                'validation_message'=>$validatedData->errors(),
                'data' => null
            ], 422);
        }
        $lead = Lead::with(['user'])
                        ->where('phone', $request['phone'])
                        ->where('member_code', $request['member_code'])
                        ->first();
        $lead->fcm_token = $request->fcm_token;
        $lead->save();
        
        if (!$lead) 
        {
            return response()->json([
                'message' => 'Invalid credentials',
                'data' => null
            ], 401);
        }
        $authToken = $lead->user->createToken('auth-token')->plainTextToken;
        return response()->json([
            'message' => 'Login successfully',
            'data' => [
                'user_info' =>$lead->user,
                'access_token' => $authToken
            ]
        ], 201);
    }


    public function logout() 
    {
        if (auth('sanctum')->id()) 
        {
            User::find(auth('sanctum')->id())->tokens()->delete();

            return response()->json([
                'message' => 'Logged Out Successfully',
                'data'=> null
            ], 201);
        }else{
            return response()->json([
                'message' => "You Didn't Login Before, Please Login First !!",
                'data'=> null
            ], 403);
        }
    }

    public function getMemberships()
    {
        if (auth('sanctum')->id()) 
        {
            $member = Lead::whereUserId(Auth('sanctum')->id())
                            ->with([
                                'memberships','memberships.service_pricelist'
                            ])->first();

            return response()->json([
                'message'=>"successfully",
                'data'=>[
                    'memberships'   => $member->memberships
                ]
            ], 201);
        }else{
            return response()->json([
                'message'=>"unauthorized",
                'data'=>null
            ], 403);
        }
        
        
    }

    public function profile()
    {
        if(! auth('sanctum')->id())
        {
            return response()->json([
                'message' =>'unauthorized!',
                'data' => null
            ],403);
        }

        $member = Lead::with(['status','address','source'])
                            ->whereUserId(auth('sanctum')->id())
                            ->first()->makeHidden([
                                'photo','downloaded_app','status_id','source_id','sales_by_id','address_id','media','source']);
        if(!$member){
            return response()->json([
                'message' =>'Can not find member',
                'data' => null
            ],403);
        }

        $memberships_query = Membership::with(['service_pricelist','invoice'])
                        ->withCount('attendances')
                        ->withSum('freezeRequests','freeze')
                        ->whereMemberId($member->id);
                        //->get();
        $memberships = $memberships_query->get();
        $current_membership =$memberships_query->whereIn('status',['current','pending'])->first();
    
        return response()->json([
            'message'=>'Successfully',
            'data'=>[
                'member'        => $member,
                'current_membership'=>$current_membership,
                'memberships'   => $memberships,
            ]
        ],200);

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
        if(!$request['name']){
            return response()->json(['message' => 'need to send name', 'validation_error'=>['name'=>'required']], 422);
        }
        $user->update([
            'name' =>  $request['name']
        ]);
        $user->lead->update(['name'=>$request['name']]);
        return response()->json(['message' => 'Updated successfully', 'data'=>$user->makeHidden('lead')], 201);
    }

    public function updateImage(Request $request)
    {
        if (!auth('sanctum')->id()) {
            return response()->json(['message' => 'UnAuthorized', 'data' => null], 403);
        }
        $member = Lead::whereType('member')
                            ->whereUserId(auth('sanctum')->user()->id)
                            ->first();

        if (!$request->file('photo'))
        {
            return response()->json(['message' => 'need to send photo', 'validation_error'=>['photo'=>'required']], 422);
        }
        if ($member->photo) {
            $member->photo->delete();
        }
        $member->addMediaFromRequest('photo')->toMediaCollection('photo');
        return response()->json(['message' => 'Updated successfully' ,'data'=>$member->fresh()], 201);
    }

    public function trainers()
    {
        if (!auth('sanctum')->check()) {
            return response()->json(['message' => 'Please login first!','data'=>null], 403);
        }

        $member = auth('sanctum')->user()->lead;

        $trainerIds = $member->memberships()
            ->with(['trainer' => function ($query) {
                $query->withSum('ratings', 'rate')->withCount('ratings');
            }])
            ->get()
            ->pluck('trainer.id')
            ->unique();

        $trainers = User::withSum('ratings', 'rate')
            ->withCount('ratings')
            ->whereIn('id', $trainerIds)
            ->get();

        return response()->json(['message'=>"completed",'data'=>['trainers' => $trainers]], 200);
    }

    public function currentTrainer()
    {
        if (!auth('sanctum')->check()) {
            return response()->json(['message' => 'Please login first!','data'=>null], 403);
        }

        $member = auth('sanctum')->user()->lead;

        $membership = $member->memberships()
            ->whereHas('service_pricelist.service', function ($query) {
                $query->where('service_type_id', $this->setting->pt_service_type);
            })
            ->with(['trainer' => function ($query) {
                $query->withSum('ratings', 'rate')->withCount('ratings');
            }])
            ->whereIn('status', ['current','pending'])
            ->latest()
            ->first();
        if (!$membership) {
            return response()->json(['message' => 'Current membership is expired','data'=>null], 402);
        }

        return response()->json(['message'=>'completed','data'=>['trainer' => $membership->trainer,'membership'=>$membership    ]], 200);
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
