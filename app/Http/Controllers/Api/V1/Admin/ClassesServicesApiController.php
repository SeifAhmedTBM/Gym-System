<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\Admin\ServiceResource;
use App\Models\FreezeRequest;
use App\Models\Lead;
use App\Models\Membership;
use App\Models\MembershipAttendance;
use App\Models\MobileSetting;
use App\Models\Pricelist;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClassesServicesApiController extends Controller
{
    private $mobile_setting;

    public function __construct(){
        $this->mobile_setting = MobileSetting::all()->first();
    }
    public function pricelist()
    {

        $service_type_id = $this->mobile_setting->classes_service_type;

        $service_type = ServiceType::with(['service_pricelists' => fn($q) => $q->where('pricelists.status','active')])->findOrFail($service_type_id);
        return response()->json([
            'message'=>"success",
            'data'=>[
                'service_type'      => $service_type->makeHidden('service_pricelists'),
                'pricelists'        => $service_type->service_pricelists->map(function($price){
                    return [
                        'id'=>$price->id,
                        'amount'=>$price->amount,
                        'session_count'=>$price->session_count,
                        'name'=>$price->name,
                    ];
                }),
            ]
        ]);

    }
    public function classes()
    {
        $service_type_id = $this->mobile_setting->classes_service_type;
        $serviceType = ServiceType::with([
            'services' => function ($query) {
                $query->with(['service_pricelist' => function ($q) {
                    $q->where('status', 'active')->whereNull('deleted_at');
                }]);
            }
        ])->findOrFail($service_type_id);


        $classes = $serviceType->services->map(function ($service) {
            return [
                // 'full'=> $service,
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'logo' =>  [
                    'url'   => $service->logo->url ?? NULL,
                    'thumbnail'   => $service->logo->thumbnail ?? NULL,
                    'preview'   => $service->logo->preview ?? NULL,
                ],
                'cover' =>[
                    'url'   => $service->cover->url ?? NULL,
                    'thumbnail'   => $service->cover->thumbnail ?? NULL,
                    'preview'   => $service->cover->preview ?? NULL,
                ],
                'price_list' => $service->service_pricelist->map(function ($price) {
                    return [
                        'id' => $price->id,
                        'name' => $price->name,
                        'amount' => $price->amount,
                        'session_count' => $price->session_count,
                        'max_count' => $price->max_count,
                        'from_time' => $price->from,
                        'to_time' => $price->to,
                        'all_branches' => $price->all_branches ,
                        'branches' => $price->branches(),
                        'all_days' => $price->full_day,
                        'days'=>$price->pricelist_days
                    ];
                }),
            ];
        });

        return response()->json([
            'message' => 'success',
            'data' => [
                'classes' => $classes,
            ]
        ]);
    }

    public function my_classes(Request $request) {
        // Check if user is authenticated
        if (!auth('sanctum')->check()) {
            return response()->json([
                'message' => 'Please login first!',
                'data' => null
            ], 403);
        }


        $member = auth('sanctum')->user()->lead;

        $memberships = $member->memberships()
            ->whereHas('service_pricelist.service', function ($query) {
                $query->where('service_type_id', $this->mobile_setting?->classes_service_type);
            })
            ->whereIn('status', ['current','pending'])
            ->latest()->get();

      
        foreach ($memberships as $membership) {
            $this->adjustMembership($membership);
        }

    
        $last_note = $member->notes()->latest()->first();

//         Get today's schedules with related session, timeslot, and trainer
//        $schedules = Schedule::with(['session', 'timeslot', 'trainer'])
//            ->where('day', date('D'))
//            ->whereHas('timeslot')
//            ->get();

        // Format data for JSON response
        $data = [
            'member' => [
                'name' => $member->name,
                'photo' => $member->photo ? $member->photo->url : asset('images/user.png'),
                'last_note' => $last_note ? $last_note->notes : 'No Notes Available',

            ],
                'classes' => $memberships->map(function($membership) {
                    return [
                    'class_cover' =>
                         [
                            'url'=>$membership->service_pricelist->service->cover->url ?? null,
                            'thumbnail'=>$membership->service_pricelist->service->cover->thumbnail ?? null,
                            'preview'=>$membership->service_pricelist->service->cover->preview ?? null,
                        ]
                  ,
                    'class_logo' =>         [
                        'url'=>$membership->service_pricelist->service->logo->url ?? null,
                        'thumbnail'=>$membership->service_pricelist->service->logo->thumbnail ?? null,
                        'preview'=>$membership->service_pricelist->service->logo->preview ?? null,
                    ],
                    'class_id' => $membership->service_pricelist->id,
                    'class_name' =>  $membership->service_pricelist->service->name . ' - '.$membership->service_pricelist->name,
                    'branches' => $membership->service_pricelist->branches(),
                    'timeline'=>$membership->service_pricelist->pricelist_days->map(function($day) use($membership) {
                        return [
                            "id"=>$day->id,
                            "day"=> $day->day,
                        ];
                    }),
                    'from' => $membership->service_pricelist->from,
                    'to' => $membership->service_pricelist->to,
                    'membership_id' => $membership->id,
                    'status' => $membership->status,
                    'start_date' => $membership->start_date,
                    'end_date' => $membership->end_date,
                    'session_count' => $membership->service_pricelist->session_count,
                    'attendances' => $membership->attendances->count(),
                    'remaining_sessions' => max(0, $membership->service_pricelist->session_count - $membership->attendances->count())
                    ];
                }),
        ];

        // Return JSON response
        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function my_classes_history(Request $request) {
        // Check if user is authenticated
        if (!auth('sanctum')->check()) {
            return response()->json([
                'message' => 'Please login first!',
                'data' => null
            ], 403);
        }

        // Fetch the authenticated member
        $member = auth('sanctum')->user()->lead;

        // Get active memberships based on service type
        $memberships = $member->memberships()
            ->whereHas('service_pricelist.service', function ($query) {
                $query->where('service_type_id', $this->mobile_setting?->classes_service_type);
            })
            ->latest()->get();



        // Adjust memberships
        foreach ($memberships as $membership) {
            $this->adjustMembership($membership);
        }

        // Fetch the latest note for the member
        $last_note = $member->notes()->latest()->first();

//         Get today's schedules with related session, timeslot, and trainer
//        $schedules = Schedule::with(['session', 'timeslot', 'trainer'])
//            ->where('day', date('D'))
//            ->whereHas('timeslot')
//            ->get();

        // Format data for JSON response
        $data = [
            'member' => [
                'name' => $member->name,
                'photo' => $member->photo ? $member->photo->url : asset('images/user.png'),
                'last_note' => $last_note ? $last_note->notes : 'No Notes Available',

            ],
            'classes' => $memberships->map(function($membership) {
                return [

                    'class_id' => $membership->service_pricelist->id,
                    'class_name' => $membership->service_pricelist->name,
                    'branches' => $membership->service_pricelist->branches(),
                    'timeline'=>$membership->service_pricelist->pricelist_days->map(function($day) use($membership) {
                        return [
                            "id"=>$day->id,
                            "day"=> $day->day,
                        ];
                    }),
                    'from' => $membership->service_pricelist->from,
                    'to' => $membership->service_pricelist->to,
                    'membership_id' => $membership->id,
                    'status' => $membership->status,
                    'start_date' => $membership->start_date,
                    'end_date' => $membership->end_date,
                    'session_count' => $membership->service_pricelist->session_count,
                    'attendances' => $membership->attendances->count(),
                    'remaining_sessions' => max(0, $membership->service_pricelist->session_count - $membership->attendances->count())
                ];
            }),
        ];

        // Return JSON response
        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }


//    public function takeAttend(Request $request)
//    {
//
//
//        if (!auth('sanctum')->check()) {
//            return response()->json([
//                'message' => 'Please login first!',
//                'data' => null
//            ], 403);
//        }
//
//        // Fetch the authenticated member
//        $member = auth('sanctum')->user()->lead->load('branch');
//        $setting = Setting::first();
//// branch id from membership_id
//        if (isset($member->id))
//        {
//            $membership = Membership::with(['service_pricelist' => fn($q) => $q->with('pricelist_days'),'member'])
//                ->find($request['membership_id']);
//
//            $branch_id = $member->branch->id;
//
//            $reminder_membership = $membership;
//
//            if(!$membership){
//                //   $membership = Membership::with(['service_pricelist' => fn($q) => $q->with('pricelist_days'),'member'])
//                //                 ->whereMemberId($member->id)
//                //                 ->whereHas('service_pricelist',function($q){
//                //                     $q->whereHas('service',function($x){
//                //                         $x->whereHas('service_type',function($p){
//                //                             $p->whereMainService(true);
//                //                         });
//                //                     });
//                //                 })
//                //                 ->whereIn('status',['expiring','current','expired'])
//                //                 // ->orderBy('id','desc')
//                //                 ->first();
//                $this->expired_membership();
//                return response()->json([
//                    'message'=>'can not find membership',
//                    'data' => null
//                ],404);
//            }
//
//
//            if ($membership->status == 'pending')
//            {
//                $start_date = Carbon::today();
//
//                if ($membership->service_pricelist->service->type == 'days')
//                {
//                    $end_date = $start_date->addDays(intval($membership->service_pricelist->service->expiry))->format('Y-m-d');
//                }else{
//                    $end_date = $start_date->addMonth(intval($membership->service_pricelist->service->expiry))->format('Y-m-d');
//                }
//
//                // $this->cannotAttend();
//                $membership->update([
//                    'start_date'        => date('Y-m-d'),
//                    'end_date'          => $end_date,
//                    'status'            => 'current'
//                ]);
//            }
//
//            if ($membership->status == 'refunded')
//            {
////                $this->refunded_membership();
//                return response()->json([
//                    'message'=>'Membership is refunded',
//                    'data' => null
//                ],404);
//                // return 1;
////                return back();
//            }
//
//            // if ($membership->status == 'expired')
//            // {
//            //     $this->expired_membership();
//            //     return back();
//            // }
//
//            // if(Auth::user()->id == 1){
//            //     dd($membership);
//            // }
//
//            if(!is_null($membership)){
//                $check_last_attend = MembershipAttendance::whereMembershipId($membership->id)->get();
//                if(count($check_last_attend) > 0)
//                {
//                    $last_attend = MembershipAttendance::whereMembershipId($membership->id)->latest()->first();
//
//                    if (date('Y-m-d',strtotime($last_attend->created_at)) == date('Y-m-d'))
//                    {
//                        $diff = Carbon::parse(date('H:i:s'))->diffInMinutes(Carbon::parse($last_attend->sign_in));
//
//                        if ($diff < 60)
//                        {
//                            return response()->json([
//                                'message'=>'Attended Already',
//                                'data' => null
//                            ],404);
////                            $this->cannotAttend();
////                            return back();
//                        }
//                    }
//                }else{
//                    $last_attend = null;
//                }
//            }else{
//                $last_attend = null;
//            }
//
//            $freeze_request = FreezeRequest::find($request['freeze_id']);
//
//            if ($last_attend && is_null($last_attend->sign_out))
//            {
//                if ($last_attend->locker == $request['locker']) {
//                    $last_attend->sign_out = date('H:i:s');
//                    $last_attend->save();
//                    return response()->json([
//                        'message'=>'Sign out successfully',
//                        'data' => null
//                    ],201);
////                    session()->flash('attended', 'Sign out successfully');
//                }else{
////                    session()->flash('user_invalid', 'Locker Number is not correct !');
//                    return response()->json([
//                        'message'=>'Locker Number is not correct',
//                        'data' => null
//                    ],404);
//                }
//
//            }else{
//
//                if ($membership)
//                {
//                    if ($membership->service_pricelist->all_branches == 'true' || $membership->member->branch_id == $branch_id)
//                    {
//                        $from = Carbon::parse($membership->service_pricelist->from)->format('H:i');
//                        $to = Carbon::parse($membership->service_pricelist->to)->format('H:i');
//
//                        if ($membership->service_pricelist->pricelist_days->count() <= 0 || in_array(date('D'),$membership->service_pricelist->pricelist_days()->pluck('day')->toArray()))
//                        {
//                            if ($membership->service_pricelist->full_day == 'true' || $membership->service_pricelist->full_day == 'false' && $from <= date('H:i') && $to >= date('H:i'))
//                            {
//                                if ($setting->has_lockers == true) {
//                                    $attend = MembershipAttendance::create([
//                                        'sign_in'           => date('H:i:s'),
//                                        'membership_id'     => $membership->id,
//                                        'locker'            => $request['locker'],
//                                        'membership_status' => $membership->status,
//                                        'branch_id'         => $request['branch_id']
//                                    ]);
//                                }else{
//                                    $attend = MembershipAttendance::create([
//                                        'sign_in'           => date('H:i:s'),
//                                        'membership_id'     => $membership->id,
//                                        'locker'            => $request['locker'],
//                                        'membership_status' => $membership->status,
//                                        'branch_id'         => $request['branch_id']
//                                    ]);
//                                }
//
//
//                                if ($freeze_request) {
//
//                                    if ($setting->freeze_duration == 'days')
//                                    {
//                                        $freeze_request_end_date = Carbon::parse($freeze_request->end_date); // end date of freeze request
//                                        $now = Carbon::now()->format('Y-m-d');  // today
//
//                                        $membership->update([
//                                            'end_date'  => date('Y-m-d', strtotime($membership->end_date. ' -' . $freeze_request_end_date->diffInDays($now) . ' Days'))
//                                        ]);
//
//                                        $freeze_request->update([
//                                            'end_date'  => date('Y-m-d', strtotime($freeze_request->end_date. ' -' . $freeze_request_end_date->diffInDays($now) . ' Days')),
//                                            'freeze'    => $freeze_request->freeze - $freeze_request_end_date->diffInDays($now),
//                                        ]);
//
//                                    }else{
//                                        $freeze_request_end_date = Carbon::parse($freeze_request->end_date); // end date of freeze request
//                                        $now = Carbon::now()->format('Y-m-d');  // today
//
//                                        $total_freeze= $freeze_request->freeze*7;
//                                        $consumed = $total_freeze - $freeze_request_end_date->diffInDays($now);
//                                        $deducted_days = ceil($consumed/7)*7;
//
//                                        $membership->update([
//                                            'end_date'  => date('Y-m-d', strtotime($membership->end_date. ' -' . $deducted_days . ' Days'))
//                                        ]);
//
//                                        $freeze_request->update([
//                                            'end_date'  => date('Y-m-d', strtotime($freeze_request->end_date. ' -' . $deducted_days . ' Days')),
//                                            'freeze'    => ceil($consumed/7),
//                                        ]);
//                                    }
//                                }
//
//                                $membership->update([
//                                    'last_attendance'      => $attend->created_at
//                                ]);
//
//                                session()->flash('attended', trans('global.attended_successfully'));
//
//                                $check_last_attend = MembershipAttendance::whereMembershipId($membership->id)->get();
//
//                                if(count($check_last_attend) == 1)
//                                {
//                                    $this->welcome_call($reminder_membership);
//                                }
//
//                            }else{
//                                session()->flash('wrong_time', trans('global.please_check'));
//                            }
//                        }else{
//                            session()->flash('wrong_time', trans('global.please_check_day'));
//                        }
//                    }else{
//                        session()->flash('cannot_attend', trans('global.please_check_branch'));
//                    }
//
//                }else{
//                    session()->flash('membership_dont_have_main_service', trans('global.membership_dont_have_main_service'));
//                }
//            }
//
//            return redirect()->route('admin.members.show',$member->id);
//        }else{
//            session()->flash('user_invalid', trans('global.member_is_not_found'));
//            // return 3;
//            return back();
//        }
//    }


}
