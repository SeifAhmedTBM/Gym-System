<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    public function index()
    {
        $trainers = User::with('ratings')->withCount('ratings')->withSum('ratings','rate')->whereHas('ratings')->whereHas('roles',function($q)
        {
            $q->where('title','Coach')->orWhere('title','Trainer');
        })->orderBy('name')->get();

        return view('admin.ratings.index',compact('trainers'));
    }

    public function show($id)
    {
        $trainer = User::with('ratings')->withCount('ratings')->withSum('ratings','rate')->whereHas('ratings')->whereHas('roles',function($q)
        {
            $q->where('title','Coach')->orWhere('title','Trainer');
        })->orderBy('name')->findOrFail($id);

        return view('admin.ratings.show',compact('trainer'));
    }
}
