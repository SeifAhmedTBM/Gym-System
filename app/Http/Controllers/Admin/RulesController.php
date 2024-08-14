<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RulesAndPolicy;
use Illuminate\Http\Request;

class RulesController extends Controller
{
    public function index()
    {
        $rule = RulesAndPolicy::first();

        return view('admin.rules.index',compact('rule'));
    }

    public function store(Request $request)
    {
        $rule = RulesAndPolicy::create([
            'description' => $request->description
        ]);

        $this->created();
        return back();
    }

    public function update(Request $request,$id)
    {
        $rule = RulesAndPolicy::find($id);
        $rule->update([
            'description' => $request->description
        ]);

        $this->updated();
        return back();
    }
}
