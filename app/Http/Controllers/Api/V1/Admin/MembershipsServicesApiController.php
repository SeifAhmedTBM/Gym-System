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

class MembershipsServicesApiController extends Controller
{
    private $mobile_setting;
    public function __construct(){
        $this->mobile_setting = MobileSetting::all()->first();
    }
    public function memberships()
    {
        $service_type_id = $this->mobile_setting->membership_service_type;
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
                'memberships' => $classes,
            ]
        ]);
    }

//    function my_classes(Request $request)  {
//        if (!auth('sanctum')->check()) {
//            return response()->json(['message' => 'Please login first!','data'=>null], 403);
//        }
//        $member = auth('sanctum')->user()->lead;
//
//        $memberships = $member->memberships()
//            ->whereHas('service_pricelist.service', function ($query) {
//                $query->where('service_type_id', $this->mobile_setting->membership_service_type);
//            })
//            ->where('status', 'current')
//            ->latest()->get();
//        if (!$memberships) {
//            return response()->json(['message' => 'Current membership is expired'], 402);
//        }
//        foreach($memberships as $membership){
//            $this->adjustMembership($membership);
//        }
//        $last_note = $member->notes()->latest()->first();
//
//        $schedules = Schedule::with(['session','timeslot','trainer'])->where('day', date('D'))->whereHas('timeslot', function($q) {
//            return $q;
//        })->get();
//
//        dd($memberships,$schedules,$last_note);
//
//    }

    public function my_membership(Request $request) {
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
                $query->where('service_type_id', $this->mobile_setting->membership_service_type);
            })
            ->where('status', 'current')
            ->latest()->get();



        // Adjust memberships
        foreach ($memberships as $membership) {
            $this->adjustMembership($membership);
        }
        // Fetch the latest note for the member
        $last_note = $member->notes()->latest()->first();
        // Format data for JSON response
        $data = [
            'member' => [
                'name' => $member->name,
                'photo' => $member->photo ? $member->photo->url : asset('images/user.png'),
                'last_note' => $last_note ? $last_note->notes : 'No Notes Available',
            ],
                'memberships' => $memberships->map(function($membership) {
                    return [
                    'pricelist_id' => $membership->service_pricelist->id,
                    'pricelist_name' => $membership->service_pricelist->name,
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
                $query->where('service_type_id', $this->mobile_setting->membership_service_type);
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


}
