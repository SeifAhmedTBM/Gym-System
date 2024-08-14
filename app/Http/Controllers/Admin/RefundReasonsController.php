<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyRefundReasonRequest;
use App\Http\Requests\StoreRefundReasonRequest;
use App\Http\Requests\UpdateRefundReasonRequest;
use App\Models\RefundReason;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class RefundReasonsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('refund_reason_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = RefundReason::query()->select(sprintf('%s.*', (new RefundReason())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'refund_reason_show';
                $editGate = 'refund_reason_edit';
                $deleteGate = 'refund_reason_delete';
                $crudRoutePart = 'refund-reasons';

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

        return view('admin.refundReasons.index');
    }

    public function create()
    {
        abort_if(Gate::denies('refund_reason_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.refundReasons.create');
    }

    public function store(StoreRefundReasonRequest $request)
    {
        $refundReason = RefundReason::create($request->all());

        return redirect()->route('admin.refund-reasons.index');
    }

    public function edit(RefundReason $refundReason)
    {
        abort_if(Gate::denies('refund_reason_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.refundReasons.edit', compact('refundReason'));
    }

    public function update(UpdateRefundReasonRequest $request, RefundReason $refundReason)
    {
        $refundReason->update($request->all());

        return redirect()->route('admin.refund-reasons.index');
    }

    public function show(RefundReason $refundReason)
    {
        abort_if(Gate::denies('refund_reason_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.refundReasons.show', compact('refundReason'));
    }

    public function destroy(RefundReason $refundReason)
    {
        abort_if(Gate::denies('refund_reason_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $refundReason->delete();

        return back();
    }

    public function massDestroy(MassDestroyRefundReasonRequest $request)
    {
        RefundReason::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
