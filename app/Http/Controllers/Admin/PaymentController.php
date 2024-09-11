<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Membership;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Exports\PaymentsExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyPaymentRequest;

class PaymentController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        // $transactions = Transaction::where('transactionable_type','App\\Models\\Payment')
        //                                 ->whereDoesntHave('transactionable')
        //                                 // ->get();
        //                                 ->delete();

        // $payments = Payment::with(['transaction','invoice'])->whereHas('invoice')->get();
        // foreach ($payments as $key => $payment) 
        // {
        //     if ($payment->transaction) 
        //     {
        //         $payment->transaction->update([
        //             'amount'        => $payment->amount,
        //             'account_id'    => $payment->account_id,
        //             'created_at'    => $payment->created_at,
        //         ]);
        //     }else{
        //         Transaction::create([
        //             'transactionable_type'  => 'App\\Models\\Payment',
        //             'transactionable_id'    => $payment->id,
        //             'amount'                => $payment->amount,
        //             'account_id'            => $payment->account_id,
        //             'created_by'            => $payment->created_by_id ?? $payment->sales_by_id,
        //             'created_at'            => $payment->created_at
        //         ]);
        //     }

        //     $payment->created_by_id = $payment->invoice->created_by_id ?? $payment->sales_by_id;
        //     $payment->save();
        // }

        abort_if(Gate::denies('payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);

        $invoice_prefix = Setting::first()->invoice_prefix;

        $employee = Auth()->user()->employee;

        if ($request->ajax()) {
            if ($employee && $employee->branch_id != NULL) 
            {
                $query = Payment::index($data)
                                    ->with(['invoice','invoice.membership','invoice.membership.member','invoice.membership.service_pricelist','account', 'sales_by','created_by'])
                                    ->whereHas('invoice',function($q){
                                        $q->whereHas('membership');
                                    })
                                    ->whereHas('account',fn($q) => $q->whereBranchId($employee->branch_id))
                                    ->latest()
                                    ->select(sprintf('%s.*', (new Payment())->table));
            }else{
                $query = Payment::index($data)
                                    ->with(['invoice','invoice.membership','invoice.membership.member','invoice.membership.service_pricelist','account', 'sales_by','created_by'])
                                    ->whereHas('invoice',function($q){
                                        $q->whereHas('membership');
                                    })
                                    ->latest()
                                    ->select(sprintf('%s.*', (new Payment())->table));
            }

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'payment_show';
                $editGate = 'payment_edit';
                $deleteGate = 'payment_delete';
                $crudRoutePart = 'payments';

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

            $table->addColumn('account', function ($row) {
                return $row->account ? $row->account->name : '';
            });

            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });

            $table->editColumn('notes', function ($row) {
                return $row->notes ? $row->notes : 'No Notes';
            });

            $table->addColumn('member_name', function ($row) {
                return $row->invoice && $row->invoice->membership ? '<a href="'.route("admin.members.show",$row->invoice->membership->member_id).'">'.$row->invoice->membership->member->name.'<br>'.$row->invoice->membership->member->memberPrefix().$row->invoice->membership->member->member_code.'<br>'.$row->invoice->membership->member->phone.'</a>' : '';
            });

            $table->addColumn('invoice', function ($row) use($invoice_prefix) {
                return $row->invoice && $row->invoice->membership ? '<a href="'.route("admin.invoices.show",$row->invoice_id).'">'.$invoice_prefix.$row->invoice->id.'</a>' : '';
            });

            $table->addColumn('transaction', function ($row) use($invoice_prefix) {
                return $row->transaction ? $row->transaction->amount : '';
            });

            $table->addColumn('status',function($row){
                return $row->invoice ? ($row->invoice->status == 'fullpayment' ? '<span class="badge badge-success p-2">'.Invoice::STATUS_SELECT[$row->invoice->status].'</span>' : '<span class="badge badge-danger p-2">'.Invoice::STATUS_SELECT[$row->invoice->status].'</span>') : '';
            });

            $table->addColumn('sales_by_name', function ($row) {
                return $row->sales_by ? $row->sales_by->name : '';
            });

            $table->addColumn('created_by', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->account && $row->account->branch ? $row->account->branch->name : '-';
            });
            
            $table->addColumn('membership', function ($row) {
                return $row->invoice && $row->invoice->membership->service_pricelist && $row->invoice->membership->service_pricelist ? $row->invoice->membership->service_pricelist->name.'<br>'.'<span class="badge p-2 badge-'.Membership::MEMBERSHIP_STATUS_COLOR[$row->invoice->membership->membership_status].'"">'.Membership::MEMBERSHIP_STATUS[$row->invoice->membership->membership_status].'</span>': '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'invoice', 'sales_by','member_name','status','membership','transaction','created_by','branch_name']);

            return $table->make(true);
        }

        $accounts = [
            ''=>'All',
            'instapay' => 'Instapay',
            'cash' => 'Cash',
            'visa' => 'Visa',
            'vodafone' => 'Vodafone',
            'valu' => 'Valu',
            'premium' => 'Premium',
            'sympl' => 'Sympl'
        ];
        
        $accounts = $accounts + Account::pluck('name', 'id')->toArray();
        $branches = Branch::pluck('name','id');

        $sales_bies = User::whereHas('roles',function($q){
            $q->where('title','Sales');
        })->pluck('name', 'id');

        if ($employee && $employee->branch_id != NULL) 
        {
            $payments = Payment::index($data)
                            ->with(['invoice' => fn($q) => $q->where('status','=','refund')])
                            ->whereHas('invoice',function($q){
                                $q->where('status','!=','refund')
                                ->whereHas('membership');
                            })
                            ->whereHas('account',fn($q) => $q->whereBranchId($employee->branch_id))
                            ->get();
        }else{
            $payments = Payment::index($data)
                            ->with(['invoice' => fn($q) => $q->where('status','=','refund')])
                            ->whereHas('invoice',function($q){
                                $q->where('status','!=','refund')
                                ->whereHas('membership');
                            })
                            ->get();
        }

        return view('admin.payments.index',compact('sales_bies','accounts','payments','branches'));
    }

    public function create()
    {
        abort_if(Gate::denies('payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoices = Invoice::pluck('discount', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sales_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.payments.create', compact('invoices', 'sales_bies'));
    }

    public function store(StorePaymentRequest $request)
    {
        $payment = Payment::create($request->all());

        return redirect()->route('admin.payments.index');
    }

    public function edit(Payment $payment)
    {
        abort_if(Gate::denies('payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoices = Invoice::withSum('payments','amount')->pluck('discount', 'id')->prepend(trans('global.pleaseSelect'), '');


        $sales_bies = User::whereHas('roles',function($q){
            $q->where('title','Sales');
        })->whereHas('employee',function($i){
            $i->whereStatus('active');
        })->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        // $sales_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $accounts = Account::pluck('name','id');

        $payment->load('invoice', 'sales_by');

        return view('admin.payments.edit', compact('invoices', 'sales_bies', 'payment','accounts'));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {   
        $account = $payment->account;
        $account->balance = $account->balance - $request->amount;
        $account->save();
        /////////

        $transaction = $payment->transaction;
        if(!$transaction){
            
            $transaction = new Transaction;
            $transaction->transactionable_type	= 'App\\Models\\Payment';
            $transaction->transactionable_id = $payment->id;
            $transaction->amount = $payment->amount;
            $transaction->account_id = $account->id;
            $transaction->created_by = Auth::user()->id;
            $transaction->created_at = $payment->created_at.date('H:i:s');
            $transaction->save();
        }
        $transaction->update([
            'amount'            => $request['amount'],
            'account_id'        => $request['account_id'],
            'created_at'        => $request['payment_date'].date('H:i:s')
        ]);

        $payment->update($request->all());
        $payment->created_at = $request['payment_date'].date('H:i:s');
        $payment->save();

        $account = Account::findOrFail($request->account_id);
        $account->balance = $account->balance + $request->amount;
        $account->save();

        $payment_id = Payment::find($payment->id);
        $invoice    = $payment_id->invoice;

        if($invoice->payments->sum('amount') == ($invoice->service_fee - $invoice->discount))
        {
            $invoice->update([
                'status' => 'fullpayment',
                'is_reviewed'   => 0
            ]);
        }else{
            $invoice->update([
                'status' => 'partial',
                'is_reviewed'   => 0
            ]);
        }

        return redirect()->route('admin.payments.index');
    }

    public function show(Payment $payment)
    {
        abort_if(Gate::denies('payment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment->load('invoice', 'sales_by');

        return view('admin.payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        abort_if(Gate::denies('payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment->load(['invoice' => fn($q) => $q->withSum('payments','amount')],'account');
       
        $payment->account->balance -= $payment->amount;
        $payment->account->save();

        $invoice = Invoice::with('payments')->findOrFail($payment->invoice_id);  
        
        if(($invoice->payments->sum('amount') - $payment->amount) == ($invoice->service_fee - $invoice->discount))
        {
            $invoice->update([
                'status' => 'fullpayment',
                'is_reviewed'   => 0
            ]);
        }else{
            $invoice->update([
                'status' => 'partial',
                'is_reviewed'   => 0
            ]);
        }

        $payment->delete();
        
        return back();
    }

    public function massDestroy(MassDestroyPaymentRequest $request)
    {
        Payment::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function export(Request $request)
    {
        return Excel::download(new PaymentsExport($request), 'Payments.xlsx');
    }
}
