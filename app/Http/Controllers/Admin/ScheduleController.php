<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyScheduleRequest;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Schedule;
use App\Models\SessionList;
use App\Models\Timeslot;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('schedule_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schedules = Schedule::with(['session', 'timeslot', 'trainer'])->get();

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        abort_if(Gate::denies('schedule_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sessions = SessionList::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $branches = Branch::get();

        $timeslots = Timeslot::get(['to', 'from', 'id']);

        $trainers = User::whereHas('roles',function($q){
            $q->where('title','Trainer');
        })->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), 'disabled');

        return view('admin.schedules.create', compact('sessions', 'timeslots', 'trainers' ,'branches'));
    }

    public function store(StoreScheduleRequest $request)
    {
        foreach ($request['day'] as $key => $day) {
            $schedule = Schedule::create([
                'session_id'        => $request['session_id'],
                'day'               => $day,
                'date'              => $request['date'],
                'timeslot_id'       => $request['timeslot_id'],
                'trainer_id'        => $request['trainer_id'],
                'comission_type'    => $request['comission_type'],
                'comission_amount'  => $request['comission_amount'] ,
                'branch_id'         => $request['branch_id'],
            ]);
        }

        $this->created();
        return redirect()->route('admin.schedules.index');
    }

    public function edit(Schedule $schedule)
    {
        abort_if(Gate::denies('schedule_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sessions = SessionList::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $timeslots = Timeslot::pluck('from', 'id')->prepend(trans('global.pleaseSelect'), '');

        $trainers = User::whereHas('roles',function($q){
            $q->where('title','Trainer');
        })->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), 'disabled');
        $schedule->load('session', 'timeslot', 'trainer');

        $branches = Branch::get();

        return view('admin.schedules.edit', compact('sessions', 'timeslots', 'trainers', 'schedule' ,'branches'));
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $schedule->update($request->all());

        return redirect()->route('admin.schedules.index');
    }

    public function show(Schedule $schedule)
    {
        abort_if(Gate::denies('schedule_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schedule->load('session', 'timeslot', 'trainer');

        return view('admin.schedules.show', compact('schedule'));
    }

    public function destroy(Schedule $schedule)
    {
        abort_if(Gate::denies('schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schedule->delete();

        return back();
    }

    public function massDestroy(MassDestroyScheduleRequest $request)
    {
        Schedule::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
