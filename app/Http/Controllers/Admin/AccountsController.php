<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\Account;
use App\Models\Transfer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyAccountRequest;

class AccountsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('account_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Account::with('branch')->select(sprintf('%s.*', (new Account())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'account_show';
                $editGate = 'account_edit';
                $deleteGate = 'account_delete';
                $crudRoutePart = 'accounts';

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
            $table->editColumn('opening_balance', function ($row) {
                return $row->opening_balance ? number_format($row->opening_balance) : '';
            });
            $table->editColumn('branch', function ($row) {
                return $row->branch_id ? $row->branch->name : '';
            });
            $table->editColumn('commission_percentage', function ($row) {
                return $row->commission_percentage ? $row->commission_percentage : 0;
            });
            $table->editColumn('branch', function ($row) {
                return $row->branch_id ? $row->branch->name : '';
            });
            $table->editColumn('balance', function ($row) {
                return $row->balance ? number_format($row->balance) : '';
            });
            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'branch']);

            return $table->make(true);
        }

        return view('admin.accounts.index');
    }

    public function create()
    {
        abort_if(Gate::denies('account_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.accounts.create', compact('branches'));
    }

    public function store(StoreAccountRequest $request)
    {
        $account = Account::create([
            'name'                  => $request['name'],
            'opening_balance'       => $request['opening_balance'],
            'commission_percentage' => $request['commission_percentage'],
            'balance'               => +$request['opening_balance'],
            'branch_id'             => $request['branch_id'],
        ]);

        return redirect()->route('admin.accounts.index');
    }

    public function edit(Account $account)
    {
        abort_if(Gate::denies('account_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.accounts.edit', compact('account', 'branches'));
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {
        $account->update([
            'name'                   => $request['name'],
            'branch_id'              => $request['branch_id'],
            'commission_percentage'  => $request['commission_percentage'],
            'manager'                => isset($request['manager']) ? true : false
        ]);

        $this->created();
        return redirect()->route('admin.accounts.index');
    }

    public function show(Account $account)
    {
        abort_if(Gate::denies('account_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.accounts.show', compact('account'));
    }

    public function destroy(Account $account)
    {
        abort_if(Gate::denies('account_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $account->delete();

        return back();
    }

    public function massDestroy(MassDestroyAccountRequest $request)
    {
        Account::whereIn('id', request('ids'))->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getAccountsByAmount(Request $request)
    {
        $accounts = Account::all();
        return response()->json(['accounts' => $accounts]);
    }

    public function statement($id)
    {
        $account = Account::findOrFail($id);
        if (config('domains')[config('app.url')]['transactions_date'] == true) {
            $transactions = Transaction::whereAccountId($account->id)
            ->whereDate('created_at', '>=', '2023-7-1')->latest()->paginate(20);
        } else {
            $transactions = Transaction::whereAccountId($account->id)->latest()->paginate(20);
        }
        // $transactions = Transaction::whereAccountId($account->id)->latest()->paginate(20);
        return view('admin.accounts.statment', compact('account', 'transactions'));
    }

    public function transfer($id)
    {
        $account = Account::findOrFail($id);
        $accounts = Account::where('id', '!=', $account->id)->pluck('name', 'id');

        return view('admin.accounts.transfer', compact('account', 'accounts'));
    }

    public function storeTransfer(Request $request, $id)
    {
        $account = Account::findOrFail($id);
        $transfer = Transfer::create([
            'from_account'      => $account->id,
            'to_account'        => $request['to_account'],
            'amount'            => $request['amount'],
            'created_by_id'     => Auth()->user()->id,
            'created_at'        => $request['created_at']
        ]);

        $account->update([
            'balance' => $account['balance'] - $request['amount']
        ]);

        $to_account = Account::findOrFail($request->to_account);

        $to_account->update([
            'balance' => $to_account['balance'] + $request['amount']
        ]);

        $transaction = Transaction::create([
            'transactionable_type' => 'App\\Models\\Transfer',
            'transactionable_id' => $transfer->id,
            'amount' => $request['amount'],
            'account_id' => $account->id,
            'created_by' => auth()->user()->id,
            'created_at'    => $request['created_at']
        ]);

        $transaction = Transaction::create([
            'transactionable_type'  => 'App\\Models\\Transfer',
            'transactionable_id'    => $transfer->id,
            'amount'                => $request['amount'],
            'account_id'            => $to_account->id,
            'created_by'            => auth()->user()->id,
            'created_at'            => $request['created_at']
        ]);

        $this->sent_successfully();

        return redirect()->route('admin.accounts.index');
    }

    public function transactions(Request $request)
    {
        // $transactions = Transaction::where('transactionable_type','App\\Models\\Payment')->get();
        // foreach ($transactions as $key => $trans) 
        // {
        //     $trans->delete();
        // }

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);
        if ($request->ajax()) {
            $query = Transaction::index($data)->with(['account', 'transactionable', 'createdBy'])->latest()->select(sprintf('%s.*', (new Transaction())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'account_show';
                $editGate = 'account_edit';
                $deleteGate = 'account_delete';
                $crudRoutePart = 'accounts';

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
            $table->editColumn('transactionable_type', function ($row) {
                return $row->transactionable_type ? "<span class='badge " . Transaction::color[$row->transactionable_type] . "'>" . Transaction::type[$row->transactionable_type] . "</span>" : '';
            });
            $table->editColumn('transactionable_id', function ($row) {
                return $row->transactionable_id ? $row->transactionable_id : '';
            });
            $table->addColumn('account', function ($row) {
                return $row->account ? $row->account->name : '';
            });
            $table->addColumn('created_by', function ($row) {
                return $row->created_by ? $row->createdBy->name : '';
            });
            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'transactionable_type']);

            return $table->make(true);
        }

        $accounts = Account::pluck('name', 'id');

        return view('admin.transactions.index', compact('accounts'));
    }
    function update_account_balance(Account $account)
    {

        $transactions = Transaction::whereAccountId($account->id)->whereDate('created_at', '>=', '2023-7-1')->get();

        foreach ($transactions as $transaction) {

            if (
                $transaction->transactionable_type == 'App\Models\Payment' ||
                ($transaction->transactionable_type == 'App\Models\Transfer' &&
                    $transaction->transactionable_type::find($transaction->transactionable_id)->to_account == $account->id) ||
                $transaction->transactionable_type == 'App\Models\ExternalPayment'
            ) {

                $account->update([
                    'balance'   =>  $account->balance + $transaction->amount
                ]);
            } else {

                $account->update([
                    'balance'   =>  $account->balance - $transaction->amount
                ]);
            }
        }
        return back();
    }
}
