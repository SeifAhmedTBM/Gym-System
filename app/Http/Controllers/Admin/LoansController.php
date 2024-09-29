<?php

namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use App\Models\User;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ExpensesCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\MassDestroyLoanRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;

class LoansController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('loan_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);

        $employee = Auth()->user()->employee;

        if ($request->ajax()) {

            if ($employee && $employee->branch_id != NULL) 
            {
                $query = Loan::index($data)
                                ->with(['employee', 'created_by','account'])
                                ->whereHas('employee',fn($q) => $q->whereBranchId($employee->branch_id))
                                ->select(sprintf('%s.*', (new Loan())->table));
            }else{
                $query = Loan::index($data)->with(['employee', 'created_by','account'])->select(sprintf('%s.*', (new Loan())->table));
            }
            
            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'loan_show';
                $editGate = 'loan_edit';
                $deleteGate = 'loan_delete';
                $crudRoutePart = 'loans';

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
            $table->addColumn('employee_job_status', function ($row) {
                return $row->employee ? $row->employee->name : '';
            });

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->addColumn('account_name', function ($row) {
                return $row->account_id ? $row->account->name : '';
            });
            
            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });
            
            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'employee', 'created_by','account_name']);

            return $table->make(true);
        }

        $created_bies = User::whereHas('roles',function($q){
                            $q->where('Title','Admin');
                        })
                        ->pluck('name', 'id');

        $loans = Loan::index($data);

        return view('admin.loans.index',compact('created_bies','loans'));
    }

    public function create()
    {
        abort_if(Gate::denies('loan_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee = Auth()->user()->employee;
        if ($employee && $employee->branch_id != NULL) 
        {
            $employees = Employee::whereBranchId($employee->branch_id)
                                            ->whereStatus('active')
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->prepend(trans('global.pleaseSelect'), '');
        }else{
            $employees = Employee::whereStatus('active')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->prepend(trans('global.pleaseSelect'), '');
        }

        $accounts = Account::all();

        $branches = Branch::pluck('name','id')->prepend(trans('global.pleaseSelect'),'');

        $selected_branch = Auth()->user()->employee->branch ?? NULL;

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.loans.create', compact('employees', 'created_bies','accounts','selected_branch','branches'));
    }

    public function store(StoreLoanRequest $request)
    {
        $loan = Loan::create([
            'employee_id'   => $request['employee_id'],
            'name'          => $request['name'],
            'amount'        => $request['amount'],
            'account_id'    => $request['account_id'],
            'created_at'    => $request['created_at'],
            'created_by_id' => Auth()->user()->id,
        ]);
        

        $transaction = Transaction::create([
            'transactionable_type' => 'App\\Models\\Loan',
            'transactionable_id' => $loan->id,
            'amount' => $loan->amount,
            'account_id' => $loan->account_id,
            'created_at' => $loan->created_at,
            'created_by' => auth()->user()->id,
        ]);
        
        $loan->account->balance = $loan->account->balance - $loan->amount;
        $loan->account->save();
      

        return redirect()->route('admin.loans.index');
    }

    public function edit(Loan $loan)
    {
        abort_if(Gate::denies('loan_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee = Auth()->user()->employee;
        if ($employee && $employee->branch_id != NULL) 
        {
            $employees = Employee::whereBranchId($employee->branch_id)
                                            ->whereStatus('active')
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->prepend(trans('global.pleaseSelect'), '');
        }else{
            $employees = Employee::whereStatus('active')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->prepend(trans('global.pleaseSelect'), '');
        }

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $accounts = Account::all();
        
        $loan->load('employee', 'created_by');

        return view('admin.loans.edit', compact('employees', 'created_bies', 'loan','accounts'));
    }

    public function update(UpdateLoanRequest $request, Loan $loan)
    {
        $loan_transaction = $loan->transaction;
        $loan_transaction->update([
            'created_at'    => $request['created_at'].date('H:i:s'),
            'account_id'    => $request['account_id'],
            'amount'        => $request['amount']
        ]);
        $loan->update($request->all());
        return redirect()->route('admin.loans.index');
    }

    public function show(Loan $loan)
    {
        abort_if(Gate::denies('loan_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $loan->load('employee', 'created_by');

        return view('admin.loans.show', compact('loan'));
    }

    public function destroy(Loan $loan)
    {
        abort_if(Gate::denies('loan_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $loan->delete();

        return back();
    }

    public function massDestroy(MassDestroyLoanRequest $request)
    {
        Loan::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
