<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ScheduleMainGroup;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ScheduleMainGroupController extends Controller
{
    public function index()
    {
        $schedule_main_groups = ScheduleMainGroup::orderBy('name')->get();

        return view('admin.schedule_main_group.index', compact('schedule_main_groups'));
    }

    public function create()
    {
        return view('admin.schedule_main_group.create');
    }

    public function store(Request $request)
    {
        $schedule_main_group = ScheduleMainGroup::create($request->all());

        return redirect()->route('admin.schedule-main-groups.index');
    }

    public function edit(ScheduleMainGroup $schedule_main_group)
    {
        return view('admin.schedule_main_group.edit', compact('schedule_main_group'));
    }

    public function update(Request $request, ScheduleMainGroup $schedule_main_group)
    {
        $schedule_main_group->update($request->all());

        return redirect()->route('admin.schedule-main-groups.index');
    }

    public function show(ScheduleMainGroup $schedule_main_group)
    {
        return view('admin.schedule_main_group.show', compact('schedule_main_group'));
    }

    public function destroy(ScheduleMainGroup $schedule_main_group)
    {        
        $schedule_main_group->delete();

        return back();
    }

}
