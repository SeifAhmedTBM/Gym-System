<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Account;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreWithdrawalRequest;
use App\Http\Requests\UpdateWithdrawalRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyWithdrawalRequest;

class WithdrawalController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('withdrawal_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);

        if ($request->ajax()) {
            $query = Withdrawal::index($data)->with(['account', 'created_by'])->select(sprintf('%s.*', (new Withdrawal())->table));

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'withdrawal_show';
                $editGate = 'withdrawal_edit';
                $deleteGate = 'withdrawal_delete';
                $crudRoutePart = 'withdrawals';

                return view('partials.datatablesActions', compact(
                'viewGate',
                // 'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
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

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'account', 'created_by']);

            return $table->make(true);
        }

        $accounts = Account::pluck('name','id');

        $created_bies = User::whereHas('roles',function($q){
            $q = $q->whereIn('Title',['Admin','Sales','Receptionist']);
        })->pluck('name','id');

        $withdrawals = Withdrawal::index($data);

        return view('admin.withdrawals.index',compact('accounts','created_bies','withdrawals'));
    }

    public function create()
    {
        abort_if(Gate::denies('withdrawal_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.withdrawals.create');
    }

    public function store(StoreWithdrawalRequest $request)
    {
        $withdrawal = Withdrawal::create([
            'amount'            => $request['amount'],
            'notes'             => $request['notes'],
            'account_id'        => $request['account_id'],
            'created_by_id'     => Auth()->user()->id,
        ]);

        // return $withdrawal;
        $withdrawal->account->update([
            'balance'       => $withdrawal->account->balance - $withdrawal->amount
        ]);

        $transaction = Transaction::create([
            'transactionable_type'  => 'App\\Models\\Withdrawal',
            'transactionable_id'    => $withdrawal->id,
            'amount'                => $withdrawal->amount,
            'account_id'            => $withdrawal->account_id,
            'created_by'            => auth()->user()->id,
        ]);

        return redirect()->route('admin.withdrawals.index');
    }

    public function edit(Withdrawal $withdrawal)
    {
        abort_if(Gate::denies('withdrawal_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $accounts = Account::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $withdrawal->load('account', 'created_by');

        return view('admin.withdrawals.edit', compact('accounts', 'withdrawal'));
    }

    public function update(UpdateWithdrawalRequest $request, Withdrawal $withdrawal)
    {
        $withdrawal = Withdrawal::with(['account','transaction'])->findOrFail($withdrawal->id);
        
        $withdrawal->account->update([
            'balance'       => $withdrawal->account->balance + $withdrawal->amount
        ]);

        $withdrawal->transaction->update([
            'amount'            => $request['amount']
        ]);

        $withdrawal->update([
            'amount'            => $request['amount'],
            'notes'             => $request['notes'],
            'account_id'        => $request['account_id'],
        ]);

        return redirect()->route('admin.withdrawals.index');
    }

    public function show(Withdrawal $withdrawal)
    {
        abort_if(Gate::denies('withdrawal_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $withdrawal->load('account', 'created_by');

        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    public function destroy(Withdrawal $withdrawal)
    {
        abort_if(Gate::denies('withdrawal_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $withdrawal->account->balance = $withdrawal->account->balance + $withdrawal->amount;
        $withdrawal->account->save();
        
        $withdrawal->delete();

        return back();
    }

    public function massDestroy(MassDestroyWithdrawalRequest $request)
    {
        Withdrawal::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
