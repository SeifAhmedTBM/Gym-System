<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Refund;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class SalesService
{
    function invoices($from,$to,$branch_id)
    {
        $invoices = Invoice::where('status','!=','refund')
                            ->whereHas('membership.service_pricelist',function($x){
                                $x->whereHas('service',fn($x) => $x->whereSalesCommission(true));
                            })
                            ->when($branch_id,fn($y) => $y->whereBranchId($branch_id))
                            ->whereDate('created_at','>=',$from)
                            ->whereDate('created_at','<=',$to)
                            ->withSum([
                                'payments'  => fn($q) => $q->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to)
                                ],'amount')
                            ->latest()
                            ->get();

        return $invoices;
    }

    public function payments($from,$to,$branch_id)
    {
        $payments = Payment::whereHas(
                        'invoice',fn($q) => $q->where('status','!=','refund')
                        ->whereHas('membership.service_pricelist',function($x){
                            $x->whereHas('service',fn($x) => $x->whereSalesCommission(true));
                        })
                        ->when($branch_id,fn($y) => $y->whereBranchId($branch_id))
                    )
                    ->whereDate('created_at','>=',$from)
                    ->whereDate('created_at','<=',$to)
                    ->latest()
                    ->get();

        return $payments;
    }

    public function refunds($from,$to,$branch_id)
    {
        $refunds = Refund::whereStatus('confirmed')
                ->whereHas(
                    'invoice',fn($q) => $q
                        ->when($branch_id,fn($y) => $y->whereBranchId($branch_id))
                        ->whereHas('membership.service_pricelist', function ($q) {
                            $q->whereHas('service',fn($x) => $x->whereSalesCommission(true));
                        })
                )
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->with([
                    'invoice.membership' => fn($q) => $q->with([
                        'member.branch','service_pricelist.service.service_type'
                    ]),  
                    'account', 
                    'created_by',
                    'invoice.sales_by'
                ])
                ->get();
    
        return $refunds;
    }

    public function service_payments($from,$to,$branch_id)
    {
        $service_payments = Payment::whereHas(
                'invoice',fn($q) => $q->where('status','!=','refund')
                ->whereHas('membership.service_pricelist',function($x){
                    $x->whereHas('service',fn($x) => $x->whereSalesCommission(true));
                })
                ->when($branch_id,fn($y) => $y->whereBranchId($branch_id))
            )
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->with([
                'invoice.membership' => fn($q) => $q->with([
                    'member.branch','service_pricelist.service.service_type'
                ]),  
                'account', 
                'created_by',
                'invoice.sales_by'
            ])  
            ->get()
            ->groupBy('invoice.membership.service_pricelist.service.service_type.name');

        return $service_payments;
    }

    public function service_refunds($from,$to,$branch_id)
    {
        $service_refunds = Refund::whereStatus('confirmed')
                ->whereHas(
                    'invoice',fn($q) => $q
                        ->when($branch_id,fn($y) => $y->whereBranchId($branch_id))
                        ->whereHas('membership.service_pricelist', function ($q) {
                            $q->whereHas('service',fn($x) => $x->whereSalesCommission(true));
                        })
                )
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->with([
                        'invoice.membership' => fn($q) => $q->with([
                            'member.branch','service_pricelist.service.service_type'
                        ]),  
                        'account', 
                        'created_by',
                        'invoice.sales_by'
                    ])
                ->get()
                ->groupBy('invoice.membership.service_pricelist.service.service_type.name');
            
        return $service_refunds;
    }
}