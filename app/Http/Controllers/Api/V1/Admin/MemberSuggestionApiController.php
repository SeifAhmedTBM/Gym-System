<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Models\MemberSuggestion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class MemberSuggestionApiController extends Controller
{
    public function store(Request $request)
    {
        if (auth('sanctum')->user()->id)
        {
            $member = Lead::whereUserId(auth('sanctum')->user()->id)->first();
    
            $suggestion = MemberSuggestion::create([
                'member_id'     => $member->id,
                'description'   => $request['description']
            ]);
    
            return response()->json([
                'message' => 'Suggestion sent successfully',
                'data'=> [
                    'suggestion' => $suggestion
                ]
            ],201);
        }else{
            return response()->json([
                'message' => 'Please Login First !'
            ],201);
        }
    }
}
