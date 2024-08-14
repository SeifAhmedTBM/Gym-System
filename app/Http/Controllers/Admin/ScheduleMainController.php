<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Timeslot;
use App\Models\SessionList;
use App\Models\ScheduleMain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;

class ScheduleMainController extends Controller
{
    public function index()
    {
        $schedule_mains = ScheduleMain::latest()->get();

        return view('admin.schedule_main.index', compact('schedule_mains'));
    }

    public function create()
    {
        $sessions = SessionList::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $timeslots = Timeslot::get(['to', 'from', 'id']);

        $trainers = User::whereHas('roles', function ($q) {
            $q->where('title', 'Trainer');
        })
            ->whereHas('employee', fn ($q) => $q->whereStatus('active'))
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');
        $branches   = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.schedule_main.create', compact('sessions', 'timeslots', 'trainers', 'branches'));
    }

    public function store(Request $request)
    {

        $schedule_main = ScheduleMain::create([
            'session_id'                => $request['session_id'],
            'date'                      => $request['date'],
            'timeslot_id'               => $request['timeslot_id'],
            'trainer_id'                => $request['trainer_id'],
            'commission_type'           => $request['commission_type'],
            'commission_amount'         => $request['commission_amount'],
            'status'                    => $request['status'],
            'schedule_main_group_id'    => $request['schedule_main_group_id'],
            'branch_id'         => $request['branch_id'],
        ]);

        foreach ($request['day'] as $key => $day) {
            $schedule = Schedule::create([
                'session_id'        => $request['session_id'],
                'day'               => $day,
                'date'              => $request['date'],
                'timeslot_id'       => $request['timeslot_id'],
                'trainer_id'        => $request['trainer_id'],
                'comission_type'    => $request['commission_type'],
                'comission_amount'  => $request['commission_amount'],
                'schedule_main_id'  => $schedule_main->id,

            ]);
        }

        $this->sent_successfully();
        return redirect()->route('admin.schedule-mains.index');
    }

    public function show($id)
    {
        $schedule_main = ScheduleMain::findOrFail($id);
        $schedule_main->load(['trainer', 'schedules', 'timeslot']);

        return view('admin.schedule_main.show', compact('schedule_main'));
    }

    public function edit($id)
    {
        $schedule_main = ScheduleMain::findOrFail($id);

        $sessions = SessionList::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $timeslots = Timeslot::get(['to', 'from', 'id']);

        $trainers = User::whereHas('roles', function ($q) {
            $q->where('title', 'Trainer');
        })
            ->whereHas('employee', fn ($q) => $q->whereStatus('active'))
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');
        $branches   = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.schedule_main.edit', compact('schedule_main', 'sessions', 'timeslots', 'trainers', 'branches'));
    }

    public function update(Request $request, $id)
    {
 
        $schedule_main = ScheduleMain::with('schedules')->findOrFail($id);

        foreach ($request['schedule_id'] as $key => $schedule_id) {
            foreach ($schedule_main->schedules as $k => $schedule) {
                $schedule->update([
                    'day'               => $request['day'][$k],
                    'session_id'        => $request['session_id'],
                    'date'              => $request['date'],

                    'timeslot_id'       => $request['timeslot_id'],
                    'trainer_id'        => $request['trainer_id'],
                    'comission_type'    => $request['commission_type'],
                    'comission_amount'  => $request['commission_amount'],
                ]);
            }
        }

        $schedule_main->update([
            'session_id'                => $request['session_id'],
            'date'                      => $request['date'],
            'timeslot_id'               => $request['timeslot_id'],
            'trainer_id'                => $request['trainer_id'],
            'commission_type'           => $request['commission_type'],
            'commission_amount'         => $request['commission_amount'],
            'schedule_main_group_id'    => $request['schedule_main_group_id'],
            'status'                    => $request['status'],
            'branch_id'         => $request['branch_id'],
        ]);

        $this->sent_successfully();
        return redirect()->route('admin.schedule-mains.index');
    }

    public function destroy($id)
    {
        $schedule_main = ScheduleMain::with(['schedules', 'membership_schedules'])->findOrFail($id);
        $schedule_main->membership_schedules()->delete();
        $schedule_main->schedules()->forceDelete();
        $schedule_main->delete();

        $this->sent_successfully();
        return back();
    }

    public function change_status(Request $request,ScheduleMain $scheduleMain)
    {
        if ($scheduleMain->status == 'active') 
        {
            $scheduleMain->update([
                'status'        => 'inactive'
            ]);
        }else{
            $scheduleMain->update([
                'status'        => 'active'
            ]);
        }

        $this->sent_successfully();
        return back();
    }
}
