<?php

namespace App\Http\Controllers\Admin;

use App\Models\Account;
use App\Models\User;
use App\Models\Branch;
use App\Models\Doctor;
use App\Models\Scheduale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\MassDestroyBranchRequest;

class BranchController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('branch_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $branches = Branch::with(['sales_manager','fitness_manager'])->orderBy('name')->get();

        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        abort_if(Gate::denies('branch_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sales_managers = User::whereRelation('roles','title','Sales Manager')->pluck('name','id');
        
        $fitness_managers = User::whereRelation('roles','title','Fitness Manager')->pluck('name','id');

        return view('admin.branches.create',compact('sales_managers','fitness_managers'));
    }

    public function store(StoreBranchRequest $request)
    {
        $branch = Branch::create($request->all());

        return redirect()->route('admin.branches.index');
    }

    public function edit(Branch $branch)
    {
        abort_if(Gate::denies('branch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sales_managers = User::whereRelation('roles','title','Sales Manager')->pluck('name','id');
        
        $fitness_managers = User::whereRelation('roles','title','Fitness Manager')->pluck('name','id');

//        dd($branch->accounts);


        return view('admin.branches.edit', compact('branch','sales_managers','fitness_managers'));
    }

    public function update(UpdateBranchRequest $request, Branch $branch)
    {
        $branch->update($request->all());

        return redirect()->route('admin.branches.index');
    }

    public function show(Branch $branch)
    {
        abort_if(Gate::denies('branch_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.branches.show', compact('branch'));
    }

    public function destroy(Branch $branch)
    {
        abort_if(Gate::denies('branch_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $branch->delete();

        return back();
    }

    public function massDestroy(MassDestroyBranchRequest $request)
    {
        Branch::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getBranchAccounts($id)
    {
        $branch = Branch::with(['accounts','online_account'])->findOrFail($id);

        return response()->json([
            'branch'        => $branch,
            'accounts'      => $branch->accounts,
            'online_account'      => $branch->online_account,
        ]);
    }
}
