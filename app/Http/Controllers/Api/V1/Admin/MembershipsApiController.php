<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Lead;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreMembershipRequest;
use App\Http\Requests\UpdateMembershipRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Admin\MembershipResource;
use App\Models\FreezeRequest;
use App\Models\MembershipServiceOptions;
use App\Models\Invitation;

use App\Models\ServiceOptionsPricelist;

class MembershipsApiController extends Controller
{
    public function index()
    {   
        if (auth('sanctum')->id()) 
        {
            $member = Lead::with(['status','address','source'])
                                ->whereUserId(auth('sanctum')->user()->id)
                                ->first();
    
            $memberships = Membership::with(['service_pricelist','sales_by','trainer'])
                                    ->withCount('attendances')
                                    ->withSum('freezeRequests','freeze')
                                    ->whereMemberId($member->id)
                                    ->latest()
                                    ->get();
            
            return response()->json([
                // 'member'        => $member,
                'memberships'   => $memberships
            ],200);
        }else{
            return response()->json([
                'message'       => 'Please Login First !'
            ],200);
        }
    }

    public function show(Membership $membership)
    {
        return $membership;
        if (auth('sanctum')->id()) 
        {
            $membership = Membership::with(['attendances','freezeRequests','invoice.payments'])
                                        ->withCount('attendances')
                                        ->findOrFail($membership);
    
            return response()->json([
                'membership'        => $membership
            ],200);
        }else{
            return response()->json([
                'message'        => "Please Login First !"
            ],200);
        }
    }



    public function member_ship_statistics(Request $request){
        $Lead = Lead::where('user_id' , $request->user_id)->first();

        $latest_membership = Membership::where('member_id', $Lead->id)->latest()->first();


        $main_membership = Membership::with([
            'member',
            'invitations',
        ])->whereId($latest_membership->id)->whereIn('status', ['current', 'expiring'])
            ->with([
                'service_pricelist' => fn($q) => $q
                    ->with([
                        'service' => fn($x) => $x->with([
                            'service_type' => fn($i) => $i->whereMainService(true)
                        ])
                    ])
            ])->whereHas('service_pricelist', function ($q) {
                $q->whereHas('service', function ($x) {
                    $x->whereHas('service_type', function ($i) {
                        $i->whereMainService(true);
                    });
                });
            })
            ->withCount('invitations')
            ->first();

            
        $counter = MembershipServiceOptions::where('service_option_pricelist_id', 1)->where('membership_id', $latest_membership->id)->count();
        

        $invitation_counter = Invitation::where('membership_id' , $latest_membership->id)->count();
        
        $pricelist_inbody = ServiceOptionsPricelist::where('pricelist_id' , $main_membership->service_pricelist_id)->where('service_option_id',1)->first();
        $total_inbody_numer = $pricelist_inbody->count;

        

            return response()->json([
                'status'              => true ,
                'inbody_count'        => $counter .'/'. $total_inbody_numer,
                'invitations_counter' => $main_membership->invitations_count .'/'. $main_membership->service_pricelist->invitation ,
            ],200);
        }
    
}