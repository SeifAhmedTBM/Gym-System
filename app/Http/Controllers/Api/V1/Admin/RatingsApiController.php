<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Lead;
use App\Models\Rating;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class RatingsApiController extends Controller
{
    public function store(Request $request)
    {
        $member = Lead::with(['status','address','source'])->whereUserId(auth('sanctum')->user()->id)->first();
        
        $membership = Membership::whereMemberId($member->id)->with('trainer')->latest()->first();

        Rating::create([
            'trainer_id' => $membership->trainer_id,
            'member_id' => $member->id,
            'rate' => $request->rate
        ]);

        return response()->json(['message' => 'Created Successfully'],201);
    }
}
