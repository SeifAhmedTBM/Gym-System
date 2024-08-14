<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTimeslotRequest;
use App\Http\Requests\StoreTimeslotRequest;
use App\Http\Requests\UpdateTimeslotRequest;
use App\Models\Timeslot;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TimeslotController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('timeslot_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $timeslots = Timeslot::all();

        return view('admin.timeslots.index', compact('timeslots'));
    }

    public function create()
    {
        abort_if(Gate::denies('timeslot_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.timeslots.create');
    }

    public function store(StoreTimeslotRequest $request)
    {
        $timeslot = Timeslot::create($request->all());

        return redirect()->route('admin.timeslots.index');
    }

    public function edit(Timeslot $timeslot)
    {
        abort_if(Gate::denies('timeslot_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.timeslots.edit', compact('timeslot'));
    }

    public function update(UpdateTimeslotRequest $request, Timeslot $timeslot)
    {
        $timeslot->update($request->all());

        return redirect()->route('admin.timeslots.index');
    }

    public function show(Timeslot $timeslot)
    {
        abort_if(Gate::denies('timeslot_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.timeslots.show', compact('timeslot'));
    }

    public function destroy(Timeslot $timeslot)
    {
        abort_if(Gate::denies('timeslot_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $timeslot->delete();

        return back();
    }

    public function massDestroy(MassDestroyTimeslotRequest $request)
    {
        Timeslot::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
