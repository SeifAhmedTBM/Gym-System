<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonthlyReportExport implements FromCollection,WithHeadings
{
    use Exportable;
    
    public function collection()
    {   
        $date = isset(request()->date) ? request()->date : date('Y-m');

        $payments = Payment::whereHas('invoice',function($q){
                                    $q->whereIn('status',['fullpayment','partial']);
                                })
                                ->with(['sales_by','account','invoice' => fn($q) => $q->with(['membership' => fn($q) => $q->with(['service_pricelist','member'])])])
                                ->whereYear('created_at',date('Y',strtotime($date)))->whereMonth('created_at',date('m',strtotime($date)))
                                ->latest()
                                ->get();
        $payments = $payments->map(function($payment){
            return [
                'id'              => $payment->id,
                'member_name'     => ($payment->invoice->membership->member->name ?? '-') ,
                'plan_name'       => ($payment->invoice->membership->service_pricelist->name ?? '-'),
                'price'           => round(($payment->invoice->net_amount/1.14),2) ?? '-',
                'vat'             => round((($payment->invoice->net_amount/1.14)*0.14),2) ?? '-',
                'net_amount'      => round($payment->invoice->net_amount,2),
                'account'         => $payment->account->name ?? '-',
                'sales_by_id'     => $payment->sales_by->name ?? '-',
                'created_at'      => $payment->created_at,
            ];
        });

        return $payments;
    }

    public function headings(): array
    {
        return [
            '#',
            'Member Name',
            'Plan Name',
            'Price',
            'VAT',
            'Net Amount',
            'Account',
            'Sales by',
            'Created At'
        ];
    }
}
