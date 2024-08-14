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
}
