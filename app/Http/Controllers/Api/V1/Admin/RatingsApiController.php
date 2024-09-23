<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Lead;
use App\Models\Rating;
use App\Models\Membership;
use App\Models\MobileSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class RatingsApiController extends Controller
{
    private $mobile_setting;
    public function __construct(){
        $this->mobile_setting = MobileSetting::all()->first();
    }
    public function store(Request $request)
    {
        $member = Lead::with(['status','address','source'])->whereUserId(auth('sanctum')->user()->id)->first();
        
        $membership = Membership::whereMemberId($member->id)->whereHas('service_pricelist.service', function ($query) {
            $query->where('service_type_id', $this->mobile_setting->pt_service_type);
        })->with('trainer')->latest()->first();

        Rating::create([
            'trainer_id' => $membership->trainer_id,
            'member_id' => $member->id,
            'rate' => $request->rate
        ]);

        return response()->json(['message' => 'Created Successfully'],201);
    }
}
