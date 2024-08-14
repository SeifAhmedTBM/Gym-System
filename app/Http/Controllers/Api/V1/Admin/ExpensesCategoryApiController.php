<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpensesCategoryRequest;
use App\Http\Requests\UpdateExpensesCategoryRequest;
use App\Http\Resources\Admin\ExpensesCategoryResource;
use App\Models\ExpensesCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpensesCategoryApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('expenses_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ExpensesCategoryResource(ExpensesCategory::all());
    }

    public function store(StoreExpensesCategoryRequest $request)
    {
        $expensesCategory = ExpensesCategory::create($request->all());

        return (new ExpensesCategoryResource($expensesCategory))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ExpensesCategory $expensesCategory)
    {
        abort_if(Gate::denies('expenses_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ExpensesCategoryResource($expensesCategory);
    }

    public function update(UpdateExpensesCategoryRequest $request, ExpensesCategory $expensesCategory)
    {
        $expensesCategory->update($request->all());

        return (new ExpensesCategoryResource($expensesCategory))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ExpensesCategory $expensesCategory)
    {
        abort_if(Gate::denies('expenses_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expensesCategory->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
