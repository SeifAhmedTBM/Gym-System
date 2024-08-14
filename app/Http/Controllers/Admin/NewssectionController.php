<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyNewssectionRequest;
use App\Http\Requests\StoreNewssectionRequest;
use App\Http\Requests\UpdateNewssectionRequest;
use App\Models\Newssection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class NewssectionController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('newssection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Newssection::query()->select(sprintf('%s.*', (new Newssection())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'newssection_show';
                $editGate = 'newssection_edit';
                $deleteGate = 'newssection_delete';
                $crudRoutePart = 'newssections';

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

        return view('admin.newssections.index');
    }

    public function create()
    {
        abort_if(Gate::denies('newssection_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.newssections.create');
    }

    public function store(StoreNewssectionRequest $request)
    {
        $newssection = Newssection::create($request->all());

        return redirect()->route('admin.newssections.index');
    }

    public function edit(Newssection $newssection)
    {
        abort_if(Gate::denies('newssection_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.newssections.edit', compact('newssection'));
    }

    public function update(UpdateNewssectionRequest $request, Newssection $newssection)
    {
        $newssection->update($request->all());

        return redirect()->route('admin.newssections.index');
    }

    public function show(Newssection $newssection)
    {
        abort_if(Gate::denies('newssection_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.newssections.show', compact('newssection'));
    }

    public function destroy(Newssection $newssection)
    {
        abort_if(Gate::denies('newssection_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $newssection->delete();

        return back();
    }

    public function massDestroy(MassDestroyNewssectionRequest $request)
    {
        Newssection::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
