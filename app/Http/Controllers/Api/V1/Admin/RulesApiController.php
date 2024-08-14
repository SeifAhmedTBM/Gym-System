<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\RulesAndPolicy;
use Illuminate\Http\Request;

class RulesApiController extends Controller
{
    public function index()
    {
        $rule = RulesAndPolicy::first();

        return response()->json([
            'description' => $rule->description
        ]);
    }
}
