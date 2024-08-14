<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyActionRequest;
use App\Http\Requests\StoreActionRequest;
use App\Http\Requests\UpdateActionRequest;
use App\Models\Action;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ActionController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('action_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Action::query()->select(sprintf('%s.*', (new Action())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'action_show';
                $editGate = 'action_edit';
                $deleteGate = 'action_delete';
                $crudRoutePart = 'actions';

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
            
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.actions.index');
    }

    public function create()
    {
        abort_if(Gate::denies('action_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.actions.create');
    }

    public function store(StoreActionRequest $request)
    {
        $action = Action::create($request->all());

        return redirect()->route('admin.actions.index');
    }

    public function edit(Action $action)
    {
        abort_if(Gate::denies('action_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.actions.edit', compact('action'));
    }

    public function update(UpdateActionRequest $request, Action $action)
    {
        $action->update($request->all());

        return redirect()->route('admin.actions.index');
    }

    public function show(Action $action)
    {
        abort_if(Gate::denies('action_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.actions.show', compact('action'));
    }

    public function destroy(Action $action)
    {
        abort_if(Gate::denies('action_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $action->delete();

        return back();
    }

    public function massDestroy(MassDestroyActionRequest $request)
    {
        Action::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
