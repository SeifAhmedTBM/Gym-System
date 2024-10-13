<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Refund;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesService
{
    function invoices($from,$to,$branch_id,$type='Super Admin')
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
                            ->latest();
        if ($type == 'Sales'){
            $invoices = $invoices->where('created_by_id',Auth()->user()->id);
        }
        $invoices=$invoices->get();

        return $invoices;
    }

    public function payments($from,$to,$branch_id,$type='Super Admin')
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
                    ->latest();
            if ($type=="Sales"){
                $payments->where('sales_by_id',Auth()->user()->id);
            }
            $payments =$payments->get();

        return $payments;
    }

    public function refunds($from,$to,$branch_id,$type='Super Admin')
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
            ]);
        if ($type == 'Sales'){
            $refunds = $refunds->where('created_by_id',Auth()->user()->id);
        }

        $refunds = $refunds->get();
    
        return $refunds;
    }

    public function service_payments($from,$to,$branch_id,$type='Super Admin')
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
            ]);
        if ($type == 'Sales'){
            $service_payments = $service_payments->where('sales_by_id',Auth()->user()->id);
        }
        $service_payments = $service_payments->get()
            ->groupBy('invoice.membership.service_pricelist.service.service_type.name');

        return $service_payments;
    }

    public function service_refunds($from,$to,$branch_id,$type='Super Admin')
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
                    ]);
        if ($type == 'Sales'){
            $service_refunds = $service_refunds->where('created_by_id',Auth()->user()->id);
        }
        $service_refunds = $service_refunds->get()
                ->groupBy('invoice.membership.service_pricelist.service.service_type.name');
            
        return $service_refunds;
    }
}