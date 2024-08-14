<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleTemplate;
use App\Models\ScheduleTemplateDay;
use Illuminate\Http\Request;

class ScheduleTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedule_templates = ScheduleTemplate::simplePaginate(10);

        return view('admin.schedule-templates.index', compact('schedule_templates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.schedule-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $schedule_template = ScheduleTemplate::create(['name' => $request['name'], 'created_by' => auth()->id()]);

        foreach ($request['days'] as $day) {
            ScheduleTemplateDay::create([
                'schedule_template_id'  => $schedule_template->id,
                'day'                   => $day,
                'from'                  => isset($request['offday'][$day]) ? NULL : ($request['from'][$day] ?? '10:00'),
                'to'                    => isset($request['offday'][$day]) ? NULL : ($request['to'][$day] ?? '18:00'),
                'is_offday'             => $request['offday'][$day] ?? 0,
                'flexible'              => $request['flexible'][$day] ?? '0',
                'working_hours'         => $request['working_hours'][$day] ?? '8'
            ]);
        }
        $this->created();
        return redirect()->route('admin.schedule-templates.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $schedule_template = ScheduleTemplate::with('days')->findOrFail($id);        
        return view('admin.schedule-templates.edit', compact('schedule_template'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $schedule_template = ScheduleTemplate::find($id);
        $schedule_template->update(['name' => $request['name']]);
        $schedule_template->days()->forceDelete();
        foreach ($request['days'] as $day) {
            ScheduleTemplateDay::create([
                'schedule_template_id'  => $schedule_template->id,
                'day'                   => $day,
                'from'                  => isset($request['offday'][$day]) ? NULL : ($request['from'][$day] ?? '10:00'),
                'to'                    => isset($request['offday'][$day]) ? NULL : ($request['to'][$day] ?? '18:00'),
                'is_offday'             => $request['offday'][$day] ?? 0,
                'flexible'              => $request['flexible'][$day] ?? '0',
                'working_hours'         => $request['working_hours'][$day] ?? '8'
            ]);
        }
        $this->updated();
        return redirect()->route('admin.schedule-templates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $schedule_template = ScheduleTemplate::findOrFail($id);
        $schedule_template->days()->delete();
        $schedule_template->delete();
        $this->deleted();
        return back();
    }

    /**
     * Ajax Get Schedule Template
     */
    public function getScheduleById($id)
    {
        $schedule_template = ScheduleTemplate::with('days')->findOrFail($id);
        return response()->json([
            'days' => $schedule_template->days,
        ]);
    }
}
