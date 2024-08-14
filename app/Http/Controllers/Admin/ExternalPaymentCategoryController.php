<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExternalPaymentCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ExternalPaymentCategoryController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('external_payment_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ExternalPaymentCategory::query()->select(sprintf('%s.*', (new ExternalPaymentCategory())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'external_payment_category_show';
                $editGate = 'external_payment_category_edit';
                $deleteGate = 'external_payment_category_delete';
                $crudRoutePart = 'external-payment-categories';

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

        return view('admin.external_payment_category.index');
    }

    public function create()
    {
        abort_if(Gate::denies('external_payment_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.external_payment_category.create');
    }

    public function store(Request $request)
    {
        ExternalPaymentCategory::create($request->all());

        $this->sent_successfully();
        return redirect()->route('admin.external-payment-categories.index');
    }

    public function show($id)
    {
        $external_payment_category = ExternalPaymentCategory::findOrFail($id);

        return view('admin.external_payment_category.show',compact('external_payment_category'));
    }

    public function edit($id)
    {
        $external_payment_category = ExternalPaymentCategory::findOrFail($id);

        return view('admin.external_payment_category.edit',compact('external_payment_category'));
    }

    public function update(Request $request, $id)
    {
        $external_payment_category = ExternalPaymentCategory::findOrFail($id);
        $external_payment_category->update($request->all());

        $this->sent_successfully();
        return redirect()->route('admin.external-payment-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $external_payment_category = ExternalPaymentCategory::findOrFail($id)->delete();

        $this->sent_successfully();
        return back();
    }
}
