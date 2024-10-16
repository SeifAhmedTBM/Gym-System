<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\paymob_transactions;

class PaymobTransactionsController extends Controller
{
    public function index(Request $request)
    {
    
        abort_if(Gate::denies('payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);

      

        $employee = Auth()->user()->employee;

        if ($request->ajax()) {
            if ($employee && $employee->branch_id != NULL) 
            {
                $query = paymob_transactions::index($data)
                                    ->whereHas('membership.member',fn($q) => $q->whereBranchId($employee->branch_id))
                                    ->latest()
                                    ->select(sprintf('%s.*', (new paymob_transactions())->table));
            }else{
                $query = paymob_transactions::index($data)
                                    ->latest()
                                    ->select(sprintf('%s.*', (new paymob_transactions())->table));
            }

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            // $table->editColumn('actions', function ($row) {
            //     $viewGate = 'payment_show';
            //     $editGate = 'payment_edit';
            //     $deleteGate = 'payment_delete';
            //     $crudRoutePart = 'payments';

            //     return view('partials.datatablesActions', compact(
            //     'viewGate',
            //     'editGate',
            //     'deleteGate',
            //     'crudRoutePart',
            //     'row'
            // ));
            // });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });

            $table->addColumn('user', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->editColumn('membership', function ($row) {
                return $row->membership ? $row->membership->service_pricelist->name : '';
            });

         

            $table->addColumn('transaction_amount', function ($row) {
                return $row->transaction_amount  ?? '';
            });

            $table->addColumn('transaction_id', function ($row) {
                return $row->transaction_id  ?? '';
            });


            $table->addColumn('orderId', function ($row) {
                return $row->orderId  ?? '';
            });


            $table->addColumn('transaction_createdAt', function ($row) {
                return $row->transaction_createdAt  ?? '';
            });

            $table->addColumn('paymentMethodType', function ($row) {
                return $row->paymentMethodType  ?? '';
            });

            $table->addColumn('paymentMethodSubType', function ($row) {
                return $row->transaction_createdAt  ?? '';
            });

            $table->addColumn('created_at', function ($row) {
                return $row->created_at  ?? '';
            });
           
            $table->rawColumns([ 'membership' ,'user','placeholder', 'paymentMethodSubType', 'paymentMethodType','transaction_createdAt','orderId','transaction_id','transaction_amount','created_at','branch_name']);

            return $table->make(true);
        }

     
        if ($employee && $employee->branch_id != NULL) 
        {
            $payments =      paymob_transactions::whereHas('membership.member',fn($q) => $q->whereBranchId($employee->branch_id))
                            ->latest()
                            ->get();
        }
        else
        {
            $payments = paymob_transactions::latest()
                            ->get();
        }

        return view('admin.payments.payMob_index',compact('payments'));
    }
}
