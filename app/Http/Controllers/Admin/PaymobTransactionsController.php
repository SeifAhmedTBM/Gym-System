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
    
        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);
        $employee = Auth()->user()->employee;
    
        if ($request->ajax()) {
            // Start the query
            $query = paymob_transactions::query();
    
            // Filter based on employee branch if applicable
            if ($employee && $employee->branch_id != NULL) {
                $query->whereHas('membership.member', fn($q) => $q->whereBranchId($employee->branch_id));
            }
    
            // Order by latest
            $query->latest();
    
            // Create DataTables instance
            $table = Datatables::eloquent($query)
                ->addColumn('placeholder', '&nbsp;')
                ->addColumn('actions', '&nbsp;')
                ->editColumn('id', fn($row) => $row->id ?? '')
                ->addColumn('user', fn($row) => $row->user ? $row->user->name : '')
                ->editColumn('membership', fn($row) => $row->membership ? $row->membership->service_pricelist->name : '')
                ->addColumn('transaction_amount', fn($row) => $row->transaction_amount ?? '')
                ->addColumn('transaction_id', fn($row) => $row->transaction_id ?? '')
                ->addColumn('orderId', fn($row) => $row->orderId ?? '')
                ->addColumn('transaction_createdAt', fn($row) => $row->transaction_createdAt ?? '')
                ->addColumn('paymentMethodType', fn($row) => $row->paymentMethodType ?? '')
                ->addColumn('paymentMethodSubType', fn($row) => $row->paymentMethodSubType ?? '')
                ->addColumn('created_at', fn($row) => $row->created_at ?? '')
                ->rawColumns(['membership', 'user', 'placeholder', 'paymentMethodSubType', 'paymentMethodType', 'transaction_createdAt', 'orderId', 'transaction_id', 'transaction_amount', 'created_at']);
    
            return $table->make(true);
        }
    
        // Non-AJAX response logic
        if ($employee && $employee->branch_id != NULL) {
            $payments = paymob_transactions::whereHas('membership.member', fn($q) => $q->whereBranchId($employee->branch_id))
                ->latest()
                ->get();
        } else {
            $payments = paymob_transactions::latest()->get();
        }
    
        return view('admin.payments.payMob_index', compact('payments'));
    }
}
