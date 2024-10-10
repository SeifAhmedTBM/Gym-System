<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Exports\ExpensesExport;
use App\Models\ExpensesCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyExpenseRequest;
use Carbon\Carbon;
class ExpensesController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('expense_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_', 'month']);
        $employee = Auth()->user()->employee;

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        if ($request->has('month')) {
            $data['date']['from'] = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->format('Y-m-d');
            $data['date']['to'] = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth()->format('Y-m-d');
        } else {
            $data['date']['from'] = $data['date']['from'] ?? $startOfMonth->format('Y-m-d');
            $data['date']['to'] = $data['date']['to'] ?? $endOfMonth->format('Y-m-d');
        }

        $query = Expense::with(['expenses_category', 'created_by', 'account']);

        if ($request->filled('branch_id')) {
            $query->whereHas('account', fn($q) => $q->whereBranchId($request->branch_id));
        }

        $query->whereBetween('date', [$data['date']['from'], $data['date']['to']]);

// dd(isset($request->relations['expenses_category']));
        if (isset($request->relations['expenses_category']['expenses_category_id'])) {
            $query->whereIn('expenses_category_id', $request->relations['expenses_category']['expenses_category_id']);
        }
        if($request->filled('expenses_category')){
            $query->where('expenses_category_id', $request->expenses_category);
        }

        if ($employee && $employee->branch_id) {
            $query->whereHas('account', fn($q) => $q->whereBranchId($employee->branch_id));
        }
        if ($request->filled('account_id')) {
            $query->whereIn('account_id', $request->account_id);
        }
        if ($request->filled('created_by_id')) {
            $query->whereIn('created_by_id', $request->created_by_id);
        }
        if ($request->filled('amount')) {
            $query->where('amount', $request->amount);
        }

        if ($request->ajax()) {
            $table = Datatables::eloquent($query)
                ->addColumn('placeholder', '&nbsp;')
                ->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                return view('partials.datatablesActions', [
                    'viewGate' => 'expense_show',
                    'editGate' => 'expense_edit',
                    'deleteGate' => 'expense_delete',
                    'crudRoutePart' => 'expenses',
                    'row' => $row
                ]);
            });

            $table->editColumn('id', fn($row) => $row->id ?? '');
            $table->editColumn('name', fn($row) => $row->name ?? '');
            $table->addColumn('account_name', fn($row) => $row->account ? $row->account->name : '');
            $table->editColumn('amount', fn($row) => $row->amount ?? '');
            $table->addColumn('expenses_category_name', fn($row) => $row->expenses_category ? $row->expenses_category->name : '');
            $table->addColumn('created_by_name', fn($row) => $row->created_by ? $row->created_by->name : '');
            $table->addColumn('branch_name', fn($row) => $row->account && $row->account->branch ? $row->account->branch->name : '-');
            $table->editColumn('created_at', fn($row) => $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '');

            $table->rawColumns(['actions', 'placeholder', 'expenses_category', 'created_by', 'account_name', 'branch_name']);

            return $table->make(true);
        }
        $status = false;
        if($request->filled('expenses_category')) {
        $expenses_categories = ExpensesCategory::where('id',$request->expenses_category)->pluck('name', 'id');
        $status = true;
        }else{
        $expenses_categories = ExpensesCategory::pluck('name', 'id');
        }
        $accounts = Account::orderBy('name')->pluck('name', 'id');
        $users = User::whereHas('employee')->orderBy('name')->pluck('name', 'id');
        $branches = Branch::pluck('name', 'id');

        $expenses = $query->get();

        return view('admin.expenses.index', compact('status','expenses_categories', 'users', 'accounts', 'expenses', 'branches'));
    }


    // public function expenses_categories(){
    //     $expenses_categories = ExpensesCategory::get();
    //     return view('admin.expenses.categories',compact('expenses_categories'));
    // }


    public function expenses_categories(Request $request)
    {
        $branches = Branch::get();

        $branchId = $request->input('branch_id');
        $selectedCategoryId = $request->input('expenses_category_id');
        $account = Account::where('branch_id', $branchId)->first();
        $date = $request->input('date') ?? date('Y-m');

        $expenses_categories_list = ExpensesCategory::with('expenses.account.branch')
            ->where('name', '!=', 'Salary')->get();


        $expenses_categories = ExpensesCategory::with('expenses.account.branch')
            ->where('name', '!=', 'Salary');

        $expenses = Expense::query();

        if ($branchId) {
            $expenses->whereHas('account', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            });
        }

        if ($selectedCategoryId) {
            $expenses->where('expenses_category_id', $selectedCategoryId);
        }

        if ($date) {
            $expenses->whereMonth('created_at', date('m', strtotime($date)))
                ->whereYear('created_at', date('Y', strtotime($date)));
        }

        $filtered_expenses = $expenses->get();
        if($selectedCategoryId){
            $expenses_categories->where('id'  , $selectedCategoryId);
        }
        $expenses_categories = $expenses_categories->get();

        foreach ($expenses_categories as $category) {
            $category->total_amount = $category->expensesCount($category->id, $branchId, $date );
        }

        $total_expenses = $expenses_categories->sum('total_amount');

        return view('admin.expenses.categories', compact('expenses_categories_list','expenses_categories', 'branches', 'branchId', 'account', 'date', 'total_expenses', 'selectedCategoryId', 'filtered_expenses'));
    }



    public function expenses_categories_show_by_filter(Request $request){

        $employee = Auth()->user()->employee;

        $data['date']['from'] = Carbon::createFromFormat('Y-m', $request->date)->startOfMonth()->format('Y-m-d');
        $data['date']['to'] = Carbon::createFromFormat('Y-m', $request->date)->endOfMonth()->format('Y-m-d');

        $query = Expense::with(['expenses_category', 'created_by', 'account']);

        if ($request->account_id) {
            $query->whereHas('account', fn($q) => $q->whereBranchId($request->account_id));
        }

        $expenses = $query
            ->whereBetween('created_at', [$data['date']['from'], $data['date']['to']])
            ->where('expenses_category_id', $request->expenses_category_id)
            ->get();

        $expenses_categories = ExpensesCategory::pluck('name','id');

        $accounts = Account::orderBy('name')->pluck('name','id');

        $users = User::whereHas('employee')->orderBy('name')->pluck('name','id');

        $branches = Branch::pluck('name','id');

        return view('admin.expenses.categories_filter',compact('expenses_categories' ,'branches' , 'users' ,'accounts' ,'expenses'));

    }

    public function create()
    {
        abort_if(Gate::denies('expense_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expenses_categories = ExpensesCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $branches = Branch::pluck('name','id')->prepend(trans('global.pleaseSelect'),'');

        $selected_branch = Auth()->user()->employee->branch ?? NULL;

        $accounts = Account::pluck('name','id');

        return view('admin.expenses.create', compact('expenses_categories', 'created_bies','branches','selected_branch','accounts'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $expense = Expense::create([
            'name' => $request->name,
            'date' => $request->date,
            'amount' => $request->amount,
            'note' => $request->note,
            'expenses_category_id' => $request->expenses_category_id,
            'account_id' => $request->account_id,
            'created_at' => $request->date,
            'created_by_id' => auth()->user()->id,
        ]);

        $expense->account->balance = $expense->account->balance - $expense->amount;
        $expense->account->save();

        $transaction = Transaction::create([
            'transactionable_type' => 'App\\Models\\Expense',
            'transactionable_id' => $expense->id,
            'amount' => $expense->amount,
            'account_id' => $expense->account_id,
            'created_at' => $request->date,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('admin.expenses.index');
    }

    public function edit(Expense $expense)
    {
        abort_if(Gate::denies('expense_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expenses_categories = ExpensesCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $expense->load('expenses_category', 'created_by');

        $selected_branch = Auth()->user()->employee->branch ?? NULL;

        if(is_null($selected_branch)){

            $accounts = Account::pluck('name','id');

        }else{

            $accounts = Account::where('branch_id',$selected_branch->id)->pluck('name','id');
            
        }

        return view('admin.expenses.edit', compact('expenses_categories', 'created_bies', 'expense','accounts','selected_branch'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $expense->account->balance = $expense->account->balance + $expense->amount;
        $expense->account->save();

        $expense->transaction->update([
            'created_at'    => $request['date'].date('H:i:s'),
            'account_id'    => $request['account_id'],
            'amount'        => $request['amount']
        ]);
        $expense->created_at = $request['date'];
        $expense->save();
        
        $expense->update($request->all());
        
        $expense->account->balance = $expense->account->balance - $request['amount'];
        $expense->account->save();

        return redirect()->route('admin.expenses.index');
    }

    public function show(Expense $expense)
    {
        abort_if(Gate::denies('expense_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense->load('expenses_category', 'created_by');

        return view('admin.expenses.show', compact('expense'));
    }

    public function destroy(Expense $expense)
    {
        abort_if(Gate::denies('expense_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense->account->balance = $expense->account->balance + $expense->amount;
        $expense->account->save();

        $expense->delete();

        return back();
    }

    public function massDestroy(MassDestroyExpenseRequest $request)
    {
        Expense::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function export(Request $request)
    {
        return Excel::download(new ExpensesExport($request), 'Expenses.xlsx');
    }
}
