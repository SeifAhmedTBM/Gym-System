<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyMasterCardRequest;
use App\Http\Requests\StoreMasterCardRequest;
use App\Http\Requests\UpdateMasterCardRequest;
use App\Models\MasterCard;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MasterCardController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('master_card_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = MasterCard::query()->select(sprintf('%s.*', (new MasterCard())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'master_card_show';
                $editGate = 'master_card_edit';
                $deleteGate = 'master_card_delete';
                $crudRoutePart = 'master-cards';

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
            $table->editColumn('master_card', function ($row) {
                return $row->master_card ? $row->master_card : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.masterCards.index');
    }

    public function create()
    {
        abort_if(Gate::denies('master_card_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.masterCards.create');
    }

    public function store(StoreMasterCardRequest $request)
    {
        $masterCard = MasterCard::create($request->all());

        return redirect()->route('admin.master-cards.index');
    }

    public function edit(MasterCard $masterCard)
    {
        abort_if(Gate::denies('master_card_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.masterCards.edit', compact('masterCard'));
    }

    public function update(UpdateMasterCardRequest $request, MasterCard $masterCard)
    {
        $masterCard->update($request->all());

        return redirect()->route('admin.master-cards.index');
    }

    public function show(MasterCard $masterCard)
    {
        abort_if(Gate::denies('master_card_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.masterCards.show', compact('masterCard'));
    }

    public function destroy(MasterCard $masterCard)
    {
        abort_if(Gate::denies('master_card_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $masterCard->delete();

        return back();
    }

    public function massDestroy(MassDestroyMasterCardRequest $request)
    {
        MasterCard::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
