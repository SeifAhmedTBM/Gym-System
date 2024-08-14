<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Http\Resources\Admin\LoanResource;
use App\Models\Loan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoansApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('loan_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new LoanResource(Loan::with(['employee', 'created_by'])->get());
    }

    public function store(StoreLoanRequest $request)
    {
        $loan = Loan::create($request->all());

        return (new LoanResource($loan))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Loan $loan)
    {
        abort_if(Gate::denies('loan_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new LoanResource($loan->load(['employee', 'created_by']));
    }

    public function update(UpdateLoanRequest $request, Loan $loan)
    {
        $loan->update($request->all());

        return (new LoanResource($loan))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Loan $loan)
    {
        abort_if(Gate::denies('loan_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $loan->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
