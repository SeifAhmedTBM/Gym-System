<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lead;
use App\Models\User;
use App\Models\Branch;
use App\Models\Refund;
use App\Models\Status;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\UserAlert;
use App\Models\Transaction;
use App\Models\RefundReason;
use Illuminate\Http\Request;
use App\Exports\RefundsExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreRefundRequest;
use App\Http\Requests\UpdateRefundRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyRefundRequest;

class RefundController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('refund_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);

        $employee = Auth()->user()->employee;

        if ($request->ajax()) {
            if ($employee && $employee->branch_id != NULL) 
            {
                $query = Refund::index($data)
                                ->with(['refund_reason', 'invoice', 'created_by','account'])
                                ->whereHas('account',fn($q) => $q->whereBranchId($employee->branch_id))
                                ->select(sprintf('%s.*', (new Refund())->table));
            }else{
                $query = Refund::index($data)
                                ->with(['refund_reason', 'invoice', 'created_by','account'])
                                ->select(sprintf('%s.*', (new Refund())->table));
            }

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'refund_show';
                $editGate = 'refund_edit';
                $deleteGate = 'refund_delete';
                $crudRoutePart = 'refunds';

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

            $table->addColumn('member', function ($row) {
                return $row->invoice && $row->invoice->membership && $row->invoice->membership->member ? Setting::first()->member_prefix. $row->invoice->membership->member->member_code.'<br>'.'<a href="'.route('admin.members.show',$row->invoice->membership->member_id).'" target="_blank">'.$row->invoice->membership->member->name.'</a>'
                .'<br/>'.'<b>'.$row->invoice->membership->member->phone .'<b>'
                .'<br/>'.'<b>'.Lead::GENDER_SELECT[$row->invoice->membership->member->gender].'</b>' : '';
                // return $row->member ? $row->member->name : '';
            });
            
            $table->addColumn('refund_reason_name', function ($row) {
                return $row->refund_reason ? $row->refund_reason->name : '';
            });

            $table->addColumn('invoice_id', function ($row) {
                return $row->invoice ? '<a href="'.route("admin.invoices.show",$row->invoice_id).'">'. $row->invoice->invoicePrefix().$row->invoice->id .'</a>' : '';
            });

            $table->addColumn('account', function ($row) {
                return $row->account ? $row->account->name : '';
            });

            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->account && $row->account->branch ? $row->account->branch->name : '-';
            });

            $table->addColumn('status', function ($row) {
                // return $row->status ? $row->status : '';
                return $row->status ? '<span class="'.\App\Models\Refund::STATUS_COLOR[$row->status].' p-2">'.\App\Models\Refund::STATUS[$row->status].'</span>' : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'refund_reason', 'invoice', 'created_by','invoice_id','status','member']);

            return $table->make(true);
        }

        $refund_reasons = RefundReason::pluck('name','id');

        $created_bies = User::whereHas('roles',function($q){
            $q = $q->whereIn('title',['Admin','Sales','Receptionist']);
        })->pluck('name', 'id');

        $accounts = Account::pluck('name','id');

        $branches = Branch::pluck('name','id');

        if ($employee && $employee->branch_id != NULL) 
        {
            $refunds = Refund::index($data)->whereHas('account',fn($q) => $q->whereBranchId($employee->branch_id));
        }else{
            $refunds = Refund::index($data);
        }

        return view('admin.refunds.index',compact('refund_reasons','created_bies','accounts','refunds','branches'));
    }

    public function create()
    {
        abort_if(Gate::denies('refund_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $refund_reasons = RefundReason::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $invoices = Invoice::pluck('discount', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.refunds.create', compact('refund_reasons', 'invoices'));
    }

    public function store(StoreRefundRequest $request)
    {
        $refund = Refund::create($request->all());

        return redirect()->route('admin.refunds.index');
    }

    public function edit(Refund $refund)
    {
        abort_if(Gate::denies('refund_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $refund_reasons = RefundReason::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $invoices = Invoice::pluck('discount', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $refund->load('refund_reason', 'invoice', 'created_by');

        return view('admin.refunds.edit', compact('refund_reasons', 'invoices', 'created_bies', 'refund'));
    }

    public function update(UpdateRefundRequest $request, Refund $refund)
    {
        $refund->update($request->all());
        
        $transaction = $refund->transaction;
        if($transaction){
            $transaction->created_at = $refund->created_at;
            $transaction->save();
        }
        return redirect()->route('admin.refunds.index');
    }

    public function show(Refund $refund)
    {
        abort_if(Gate::denies('refund_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $refund->load('refund_reason', 'invoice', 'created_by');

        return view('admin.refunds.show', compact('refund'));
    }

    public function destroy(Refund $refund)
    {
        abort_if(Gate::denies('refund_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $refund->delete();

        return back();
    }

    public function massDestroy(MassDestroyRefundRequest $request)
    {
        Refund::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function requests(Request $request)
    {
        abort_if(Gate::denies('refund_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);

        if ($request->ajax()) {
            $query = Refund::index($data)->with(['refund_reason', 'invoice', 'created_by','account'])->whereIn('status',['rejected','pending'])->select(sprintf('%s.*', (new Refund())->table));
            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $crudRoutePart = 'refunds';
                $deleteGate = 'refund_delete';

                return view('partials.datatablesActions', compact(
                'crudRoutePart',
                'deleteGate',
                'row'
            ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('refund_reason_name', function ($row) {
                return $row->refund_reason ? $row->refund_reason->name : '';
            });

            $table->addColumn('invoice_id', function ($row) {
                return $row->invoice ? $row->invoice->invoicePrefix().$row->invoice->id : '';
            });

            $table->addColumn('account', function ($row) {
                return $row->account ? $row->account->name : '';
            });

            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->addColumn('status', function ($row) {
                // return $row->status ? $row->status : '';
                return $row->status ? \App\Models\Refund::STATUS[$row->status] : '';
            });

            $table->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'refund_reason', 'invoice', 'created_by','status']);

            return $table->make(true);
        }

        $refund_reasons = RefundReason::pluck('name','id');

        $created_bies = User::whereHas('roles',function($q){
            $q = $q->whereIn('title',['Admin','Sales','Receptionist']);
        })->pluck('name', 'id');

        $accounts = Account::pluck('name','id');

        return view('admin.refunds.requests',compact('refund_reasons','created_bies','accounts'));
    }

    public function confirm($id)
    {
        DB::transaction(function () use ($id) {
            $refund = Refund::with(['invoice','account','invoice.membership','invoice.membership.member'])->findOrFail($id);
            
            $refund->account->balance = $refund->account->balance - $refund->amount;
            $refund->account->save();

            $transaction = Transaction::create([
                'transactionable_type' => 'App\\Models\\Refund',
                'transactionable_id' => $refund->id,
                'amount' => $refund->amount,
                'account_id' => $refund->account_id,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => auth()->user()->id,
            ]);

            $refund->invoice->membership->member->update([
                'status_id'     => Status::firstOrCreate(
                    ['name' => 'Refunded member'],
                    ['color' => 'danger', 'default_next_followup_days' => 1, 'need_followup' => 'yes'])->id
            ]);
    
            $refund->status         = 'confirmed';
            $refund->created_at     = date('Y-m-d H:i:s');
            $refund->save();
    
            $refund->invoice->status = 'refund';
            $refund->invoice->save();
    
            $refund->invoice->membership->status = 'refunded';
            $refund->invoice->membership->save();

            ////////////// Alerts
            $user_alert = UserAlert::create([
                'alert_text'        => 'Refund request #'.$refund->id.' Has Been '.$refund->status,
                'alert_link'        => route('admin.refunds.show',$refund->id),
            ]);
            
            $admins = User::whereHas('roles', function($q) {
                $q = $q->whereIn('title', ['Developer','Sales','Receptionist']);
            })->pluck('name', 'id');

            foreach($admins as $id => $admin) 
            {
                DB::table('user_user_alert')->insert(['user_alert_id' => $user_alert->id, 'user_id' => $id, 'read' => 0]);
            }
            //////////////
    
            $this->sent_successfully();
        });
        
        return back();
        
    }

    public function reject($id)
    {
        $refund = Refund::findOrFail($id);
        $refund->update([
            'status'        => 'rejected',
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        ////////////// Alerts
        $user_alert = UserAlert::create([
            'alert_text'        => 'Refund request #'.$refund->id.' Has Been '.$refund->status,
            'alert_link'        => route('admin.refunds.show',$refund->id),
        ]);
        
        $admins = User::whereHas('roles', function($q) {
            $q = $q->whereIn('title', ['Developer','Sales','Receptionist']);
        })->pluck('name', 'id');

        foreach($admins as $id => $admin) 
        {
            DB::table('user_user_alert')->insert(['user_alert_id' => $user_alert->id, 'user_id' => $id, 'read' => 0]);
        }
        //////////////

        $this->sent_successfully();
        return back();
    }

    public function approve($id)
    {
        $refund = Refund::findOrFail($id);
        $refund->update([
            'status' => 'approved'
        ]);

        ////////////// Alerts
        $user_alert = UserAlert::create([
            'alert_text'        => 'Refund request #'.$refund->id.' Has Been '.$refund->status,
            'alert_link'        => route('admin.refunds.show',$refund->id),
        ]);
        
        $admins = User::whereHas('roles', function($q) {
            $q = $q->whereIn('title', ['Developer','Sales','Receptionist']);
        })->pluck('name', 'id');

        foreach($admins as $id => $admin) 
        {
            DB::table('user_user_alert')->insert(['user_alert_id' => $user_alert->id, 'user_id' => $id, 'read' => 0]);
        }
        //////////////

        $this->sent_successfully();
        return back();
    }

    public function export(Request $request)
    {
        return Excel::download(new RefundsExport($request), 'Refunds.xlsx');
    }
}
