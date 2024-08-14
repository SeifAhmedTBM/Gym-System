<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;

class AttendanceSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attendance_settings = AttendanceSetting::simplePaginate(10);
        return view('admin.attendance_settings.index', compact('attendance_settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.attendance_settings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach($request['from'] as $key => $from) {
            AttendanceSetting::create([
                'from'              => $from,
                'to'                => $request['to'][$key],
                'deduction'         => $request['deduction'][$key]
            ]);
        }
        $this->created();
        return redirect()->route('admin.attendance-settings.index');
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
        $attendance_setting = AttendanceSetting::findOrFail($id);
        return view('admin.attendance_settings.edit', compact('attendance_setting'));
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
        AttendanceSetting::find($id)->update($request->all());
        $this->updated();
        return redirect()->route('admin.attendance-settings.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attendance_setting = AttendanceSetting::findOrFail($id);
        $attendance_setting->delete();
        $this->deleted();
        return back();
    }
}
