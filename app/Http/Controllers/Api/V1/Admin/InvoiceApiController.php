<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Lead;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\Admin\InvoiceResource;
use App\Models\Membership;
use Symfony\Component\HttpFoundation\Response;

class InvoiceApiController extends Controller
{
    public function index()
    {
        if(! auth('sanctum')->id())
        {
            return response()->json([
                'message' =>'unauthorized!',
                'data' => null
            ],403);
        }
        // abort_if(Gate::denies('invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $memberships = Membership::with([
        //     'service_pricelist' => fn($q) => $q->with('service'),
        //     'invoice' => fn($q) => $q->withSum('payments','amount')->with(['payments' => fn($q) => $q->with(['sales_by','account'])]), 
        //     ])->withCount('payments')->whereMemberId($member->id)->latest()->get();

        $invoices = Invoice::with(['membership.service_pricelist.service','payments'])
                                ->whereHas('membership.member',fn($q) => $q->whereUserId(auth('sanctum')->id()))
                                ->withSum('payments','amount')
                                ->withCount('payments')
                                ->latest()
                                ->get()
                                ->map(function($invoice){
                                    return [
                                        'id'                        => $invoice->id,
                                        'start_date'                => $invoice->membership->start_date ?? '-',
                                        'end_date'                  => $invoice->membership->end_date ?? '-',
                                        'service_pricelist_name'    => $invoice->membership->service_pricelist->service->name .' - ' .$invoice->membership->service_pricelist->name ?? '-',
                                        'service_pricelist_amount'  => $invoice->membership->service_pricelist->amount ?? '0',
                                        'discount'                  => $invoice->discount ?? '0',
                                        'net_amount'                => $invoice->net_amount ?? '0',
                                        'paid'                      => $invoice->payments_sum_amount ?? '0',
                                        'rest'                      => $invoice->rest ?? '0',
                                        'status'                    => $invoice->status ?? '0',
                                        'created_at'                => $invoice->created_at ?? '0',
                                        'payments'                  => $invoice->payments,
                                    ];
                                });

        return response()->json([
            'message' =>'successfully',
            'data'=> [
                'invoices' => $invoices
            ]
        ],200);
    }

    public function store(StoreInvoiceRequest $request)
    {
        // $invoice = Invoice::create($request->all());

        // return (new InvoiceResource($invoice))
        //     ->response()
        //     ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Invoice $invoice)
    {
        $invoice = Invoice::with(['membership.service_pricelist.service','payments'])
                            ->withSum('payments','amount')
                            ->withCount('payments')
                            ->findOrFail($invoice->id);

        $data = [
            'id'                        => $invoice->id,
            'start_date'                => $invoice->membership->start_date ?? '-',
            'end_date'                  => $invoice->membership->end_date ?? '-',
            'service_pricelist_name'    => $invoice->membership->service_pricelist->name ?? '-',
            'service_pricelist_amount'  => $invoice->membership->service_pricelist->amount ?? '0',
            'discount'                  => $invoice->discount ?? '0',
            'net_amount'                => $invoice->net_amount ?? '0',
            'paid'                      => $invoice->payments_sum_amount ?? '0',
            'rest'                      => $invoice->rest ?? '0',
            'status'                    => $invoice->status ?? '0',
            'created_at'                => $invoice->created_at ?? '0',
            'payments'                  => $invoice->payments,
        ];

        return response()->json(['invoice' => $data],200);
        // abort_if(Gate::denies('invoice_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // return new InvoiceResource($invoice->load(['membership', 'sales_by']));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $invoice->update($request->all());

        return (new InvoiceResource($invoice))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoice->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
