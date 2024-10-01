<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ExternalPayment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Models\ExternalPaymentCategory;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\StoreExternalPaymentRequest;
use App\Http\Requests\UpdateExternalPaymentRequest;
use App\Http\Requests\MassDestroyExternalPaymentRequest;
use Carbon\Carbon;

class ExternalPaymentController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
//        dd($request->input('relations.account.account_id')[0]);
        abort_if(Gate::denies('external_payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);

        $employee = Auth()->user()->employee;
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $data['created_at']['from'] = isset($data['created_at']['from']) ? $data['created_at']['from'] : $startOfMonth;
        $data['created_at']['to'] = isset($data['created_at']['to']) ? $data['created_at']['to'] : $endOfMonth;


        if ($request->ajax()) {
            if ($employee && $employee->branch_id != NULL) 
            {
                $query = ExternalPayment::index($data)
                                        ->with(['account', 'created_by','external_payment_category'])
                                        ->whereHas('account',fn($q) => $q->whereBranchId($employee->branch_id))
                                        ->select(sprintf('%s.*', (new ExternalPayment())->table));
            }else{
                $query = ExternalPayment::index($data)->with(['account', 'created_by','external_payment_category','lead'])->select(sprintf('%s.*', (new ExternalPayment())->table));
            }

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'external_payment_show';
                $editGate = 'external_payment_edit';
                $deleteGate = 'external_payment_delete';
                $crudRoutePart = 'external-payments';

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

            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });

            $table->addColumn('lead_name', function ($row) {
                return $row->lead_id != NULL ? $row->lead->name . '<br/>' . "<span class='font-weight-bold text-primary'><i class='fa fa-lg fa-phone'></i> ".$row->lead->phone."</span>" : '---';
            });

            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });

            $table->editColumn('notes', function ($row) {
                return $row->notes ? $row->notes : '';
            });

            $table->addColumn('account_name', function ($row) {
                return $row->account ? $row->account->name : '';
            });

            $table->addColumn('external_payment_category_name', function ($row) {
                return $row->external_payment_category ? $row->external_payment_category->name : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->account && $row->account->branch ? $row->account->branch->name : '-';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'lead', 'placeholder', 'account', 'created_by','branch_name','lead_name']);

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

        $external_payment_categories = ExternalPaymentCategory::pluck('name','id');

        $created_bies = User::whereHas('roles',function($q){
            $q = $q->whereIn('title',['Admin','Sales','Receptionist']);
        })->pluck('name', 'id');

        if ($employee && $employee->branch_id != NULL)
        {
            $externalPayments = ExternalPayment::index($data)->whereHas('account',fn($q) => $q->whereBranchId($employee->branch_id));
        }else{
            $externalPayments = ExternalPayment::index($data);
        }
        return view('admin.externalPayments.index',compact('accounts','created_bies','externalPayments','external_payment_categories','branches'));
    }

    public function create()
    {
        abort_if(Gate::denies('external_payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $branches = Branch::pluck('name','id')->prepend(trans('global.pleaseSelect'),'');

        $selected_branch = Auth()->user()->employee->branch ?? NULL;

        $accounts = Account::pluck('name','id');

        $external_payment_categories = ExternalPaymentCategory::pluck('name','id');

        return view('admin.externalPayments.create', compact('accounts','branches','selected_branch','external_payment_categories'));
    }

    public function store(StoreExternalPaymentRequest $request)
    {
        $external_payment_category = ExternalPaymentCategory::findOrFail($request['external_payment_category_id']);

        // return $external_payment_category;

        $externalPayment = ExternalPayment::create([
            'title'                         => $request['title'] != NULL ? $request['title'] : $external_payment_category->name.' - '.$request['amount'] .' - '. $request['date'].' '.date('h:i:s'),
            'amount'                        => $request['amount'],
            'notes'                         => $request['notes'],
            'account_id'                    => $request['account_id'],
            'created_by_id'                 => auth()->user()->id,
            'lead_id'                       => $request['lead_id'],
            'external_payment_category_id'  => $request['external_payment_category_id'],
            'created_at'                    => $request['date'].' '.date('H:i:s'),
        ]);
        
        $externalPayment->account->balance = $externalPayment->account->balance + $externalPayment->amount;
        $externalPayment->account->save();

        $transaction = Transaction::create([
            'transactionable_type'  => 'App\\Models\\ExternalPayment',
            'transactionable_id'    => $externalPayment->id,
            'amount'                => $externalPayment->amount,
            'account_id'            => $externalPayment->account_id,
            'created_by'            => auth()->user()->id,
            'created_at'            => $request->date,
        ]);

        $this->sent_successfully();
        return redirect()->route('admin.external-payments.index');
    }

    public function edit(ExternalPayment $externalPayment)
    {
        abort_if(Gate::denies('external_payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $branches = Branch::pluck('name','id')->prepend(trans('global.pleaseSelect'),'');

        $selected_branch = Auth()->user()->employee->branch ?? NULL;

        $accounts = Account::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $externalPayment->load('account', 'created_by');

        $external_payment_categories = ExternalPaymentCategory::pluck('name','id');

        return view('admin.externalPayments.edit', compact('accounts', 'externalPayment','external_payment_categories','branches','selected_branch'));
    }

    public function update(UpdateExternalPaymentRequest $request, ExternalPayment $externalPayment)
    {
        $external_payment_category = ExternalPaymentCategory::findOrFail($request['external_payment_category_id']);

        $transaction = $externalPayment->transaction;
        $transaction->created_at    =  $request->created_at;
        $transaction->amount        =  $request->amount;
        $transaction->account_id    =  $request->account_id;
        $transaction->save();

        $externalPayment->update([
            'title'                         => $request['title'] != NULL ? $request['title'] : $external_payment_category->name.' - '.$request['amount'] .' - '. $request['date'].' '.date('h:i:s'),
            'amount'                        => $request['amount'],
            'notes'                         => $request['notes'],
            'account_id'                    => $request['account_id'],
            'created_by_id'                 => auth()->user()->id,
            'lead_id'                       => $request['lead_id'],
            'external_payment_category_id'  => $request['external_payment_category_id'],
            'created_at'                    => $request['created_at'].' '.date('H:i:s'),
        ]);
        
        return redirect()->route('admin.external-payments.index');
    }

    public function show(ExternalPayment $externalPayment)
    {
        abort_if(Gate::denies('external_payment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $externalPayment->load('account', 'created_by','external_payment_category');

        return view('admin.externalPayments.show', compact('externalPayment'));
    }

    public function destroy(ExternalPayment $externalPayment)
    {
        abort_if(Gate::denies('external_payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $externalPayment->account->balance = $externalPayment->account->balance - $externalPayment->amount;
        $externalPayment->account->save();
        
        $externalPayment->delete();

        return back();
    }

    public function massDestroy(MassDestroyExternalPaymentRequest $request)
    {
        ExternalPayment::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
