<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyLockerRequest;
use App\Http\Requests\StoreLockerRequest;
use App\Http\Requests\UpdateLockerRequest;
use App\Models\Locker;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class LockerController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('locker_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Locker::query()->select(sprintf('%s.*', (new Locker())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'locker_show';
                $editGate = 'locker_edit';
                $deleteGate = 'locker_delete';
                $crudRoutePart = 'lockers';

                return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            
            $table->editColumn('code', function ($row) {
                return $row->code ? $row->code : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.lockers.index');
    }

    public function create()
    {
        abort_if(Gate::denies('locker_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.lockers.create');
    }

    public function store(StoreLockerRequest $request)
    {
        $locker = Locker::create($request->all());

        return redirect()->route('admin.lockers.index');
    }

    public function edit(Locker $locker)
    {
        abort_if(Gate::denies('locker_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.lockers.edit', compact('locker'));
    }

    public function update(UpdateLockerRequest $request, Locker $locker)
    {
        $locker->update($request->all());

        return redirect()->route('admin.lockers.index');
    }

    public function show(Locker $locker)
    {
        abort_if(Gate::denies('locker_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.lockers.show', compact('locker'));
    }

    public function destroy(Locker $locker)
    {
        abort_if(Gate::denies('locker_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $locker->delete();

        return back();
    }

    public function massDestroy(MassDestroyLockerRequest $request)
    {
        Locker::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
