<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Refund;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SalesTier;
use Illuminate\Http\Request;

class TrainerService
{
    function trainer_report(Request $request,$branch_id)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $trainer_id = isset($request['trainer_id']) ? $request['trainer_id'] : NULL;

        $previous_month_start = Carbon::parse($date)->subMonth(1)->format('Y-m-01');
        $previous_month_end = Carbon::parse($date)->subMonth(1)->format('Y-m-t');

        $trainers = User::whereRelation('roles','title','Trainer')
                    ->whereHas(
                        'employee',fn($q) => $q->whereStatus('active')
                                            ->when($branch_id, fn ($y) => $y->whereBranchId($branch_id))
                    )
                    ->with([
                        'employee.branch',
                        'trainer_memberships' => fn($q) => $q
                            ->withSum([
                                'payments' => fn($x) => $x->whereMonth('payments.created_at', date('m', strtotime($date)))
                                                        ->whereYear('payments.created_at', date('Y', strtotime($date)))
                            ],'amount')
                            ->withCount([
                                'attendances' => fn($x) => $x 
                                                        ->whereMonth('created_at', date('m', strtotime($date)))
                                                        ->whereYear('created_at', date('Y', strtotime($date)))
                            ])
                            ->with([
                                'service_pricelist',
                                'invoice' 
                            ]),
                        'previous_trainer_memberships' => fn($q) => $q
                            ->withSum([
                                'payments' => fn($x) => $x
                                            ->whereDate('payments.created_at','>=',$previous_month_start)
                                            ->whereDate('payments.created_at','<=',$previous_month_end)
                            ],'amount')
                            ->withCount([
                                'attendances'   => fn($x) => $x
                                                    ->whereDate('created_at','>=',$previous_month_start)
                                                    ->whereDate('created_at','<=',$previous_month_end)
                            ])
                            ->with([
                                'service_pricelist',
                                'invoice'
                            ]),
                    ])
                     ->withSum([
                            'trainer_invoices' => fn($x) => $x->whereMonth('invoices.created_at', date('m', strtotime($date)))
                                                        ->whereYear('invoices.created_at', date('Y', strtotime($date)))
                        ],'net_amount')
                    ->whereHas('trainer_memberships',fn($q) => $q->whereHas(
                                'invoice',fn($x) => $x->where('status','!=','refund')
                                ->whereMonth('created_at', date('m', strtotime($date)))
                                ->whereYear('created_at', date('Y', strtotime($date)))
                            )
                        )
                    ->when($trainer_id,fn($q) => $q->whereId($trainer_id))
                    ->get()
                    ->map(function($trainer) use ($date){
                        $attendances_this_month         = $trainer->trainer_memberships->sum('attendances_count');
                        $attendances_previous_month     = $trainer->previous_trainer_memberships->sum('attendances_count');

                        return [
                            'id'                                => $trainer->id,
                            'name'                              => $trainer->name,
                            'branch_name'                       => $trainer->employee->branch->name ?? '-',
                            'total_invoices'                    => $trainer->trainer_invoices_sum_net_amount ?? 0,
                            'total_payments_this_month'         => number_format($trainer->trainer_memberships->sum('payments_sum_amount') ?? 0),
                            'total_payments_previous_month'     => number_format($trainer->previous_trainer_memberships->sum('payments_sum_amount') ?? 0),
                            'attendances_this_month'           => $attendances_this_month,
                            'attendances_previous_month'       => $attendances_previous_month,
                            'commissions_this_month'           => 0,
                            'commissions_previous_month'       => 0,
                            'total_commissions'                => 0,
                        ];
                    });

        return $trainers;
    } 
    
    function trainer_show(Request $request,$trainer_id)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $previous_month_start = Carbon::parse($date)->subMonth(1)->format('Y-m-01');
        $previous_month_end = Carbon::parse($date)->subMonth(1)->format('Y-m-t');

        $trainer = User::whereRelation('roles','title','Trainer')
                    ->with([
                        'employee.branch',
                        'trainer_memberships' => fn($q) => $q
                            ->withSum([
                                'payments' => fn($x) => $x->whereMonth('payments.created_at', date('m', strtotime($date)))
                                                        ->whereYear('payments.created_at', date('Y', strtotime($date)))
                            ],'amount')
                            ->withCount([
                                'attendances' => fn($x) => $x 
                                                        ->whereMonth('created_at', date('m', strtotime($date)))
                                                        ->whereYear('created_at', date('Y', strtotime($date))),
                                'payments' => fn($x) => $x->whereMonth('payments.created_at', date('m', strtotime($date)))
                                                        ->whereYear('payments.created_at', date('Y', strtotime($date)))
                            ])
                            ->with([
                                'service_pricelist',
                                'invoice'
                            ]),
                        'previous_trainer_memberships' => fn($q) => $q
                            ->withSum([
                                'payments' => fn($x) => $x
                                            ->whereDate('payments.created_at','>=',$previous_month_start)
                                            ->whereDate('payments.created_at','<=',$previous_month_end)
                            ],'amount')
                            ->withCount([
                                'attendances'   => fn($x) => $x
                                                    ->whereDate('created_at','>=',$previous_month_start)
                                                    ->whereDate('created_at','<=',$previous_month_end),
                                'payments' => fn($x) => $x
                                                    ->whereDate('payments.created_at','>=',$previous_month_start)
                                                    ->whereDate('payments.created_at','<=',$previous_month_end)
                            ])
                            ->with([
                                'service_pricelist',
                                'invoice'
                            ]),
                    ])
                     ->withSum([
                            'trainer_invoices' => fn($x) => $x->whereMonth('invoices.created_at', date('m', strtotime($date)))
                                                        ->whereYear('invoices.created_at', date('Y', strtotime($date)))
                        ],'net_amount')
                    ->whereHas('trainer_memberships',fn($q) => $q->whereHas(
                                'invoice',fn($x) => $x->where('status','!=','refund')
                                ->whereMonth('created_at', date('m', strtotime($date)))
                                ->whereYear('created_at', date('Y', strtotime($date)))
                            )
                        )
                    ->withCount([
                        'trainer_memberships'
                    ])
                    ->when($trainer_id,fn($q) => $q->whereId($trainer_id))
                    ->find($trainer_id);
                    
        return $trainer;
    }

    public function trainer_service_payments($date,$trainer_id)
    {
        $trainer_service_payments = Payment::whereHas(
                'invoice',fn($q) => $q->where('status','!=','refund')
                    ->whereHas(
                        'membership',fn($y) =>$y
                            ->whereTrainerId($trainer_id)
                            ->whereHas('service_pricelist.service',fn($x) => $x->whereSalesCommission(false))
                    )
            )
            ->whereYear('created_at',date('Y', strtotime($date)))
            ->whereMonth('created_at',date('m', strtotime($date)))
            ->with([
                    'invoice.membership' => fn($q) => $q->with([
                        'member.branch','service_pricelist.service.service_type','trainer','assigned_coach'
                    ]), 
                    'account', 
                    'created_by',
                    'invoice.sales_by'
                ])
            ->get()
            ->groupBy('invoice.membership.service_pricelist.service.service_type.name');

        return $trainer_service_payments;
    }

    public function trainer_payments($date,$trainer_id)
    {
        $payments = Payment::whereHas(
                        'invoice',fn($q) => $q->where('status','!=','refund')
                        ->whereHas(
                            'membership',fn($y) =>$y
                                ->whereTrainerId($trainer_id)
                                ->whereHas('service_pricelist.service',fn($x) => $x->whereSalesCommission(false))
                        )
                    )
                    ->whereYear('created_at',date('Y', strtotime($date)))
                    ->whereMonth('created_at',date('m', strtotime($date)))
                    ->with([
                        'invoice.membership' => fn($q) => $q->with([
                            'member.branch','service_pricelist.service.service_type','trainer','assigned_coach'
                        ]), 
                        'account', 
                        'created_by',
                        'invoice.sales_by'
                    ])
                    ->latest()
                    ->get();

        return $payments;
    }

    function invoices($from,$to,$branch_id)
    {
        $invoices = Invoice::where('status','!=','refund')
                            ->whereHas('membership.service_pricelist',function($x){
                                $x->whereHas('service',fn($x) => $x->whereSalesCommission(false));
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
                            $x->whereHas('service',fn($x) => $x->whereSalesCommission(false));
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
                            $q->whereHas('service',fn($x) => $x->whereSalesCommission(false));
                        })
                )
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->with([
                    'invoice.membership' => fn($q) => $q->with([
                            'member.branch','service_pricelist.service.service_type','trainer','assigned_coach'
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
                    $x->whereHas('service',fn($x) => $x->whereSalesCommission(false));
                })
                ->when($branch_id,fn($y) => $y->whereBranchId($branch_id))
            )
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->with([
                    'invoice.membership' => fn($q) => $q->with([
                        'member.branch','service_pricelist.service.service_type','trainer','assigned_coach'
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
                            $q->whereHas('service',fn($x) => $x->whereSalesCommission(false));
                        })
                )
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->with([
                    'invoice.membership' => fn($q) => $q->with([
                            'member.branch','service_pricelist.service.service_type','trainer','assigned_coach'
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