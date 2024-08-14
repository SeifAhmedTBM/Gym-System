<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvoicesExport implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
        $invoices = Invoice::index(request()->all())
                            ->with(['membership', 'membership.service_pricelist', 'membership.member', 'sales_by', 'payments', 'created_by','membership.trainer'])
                            ->withSum('payments','amount')
                            ->latest()
                            ->get();
        $invoices = $invoices->map(function ($invoice) {
            return [
                'id'                     => \App\Models\Setting::first()->invoice_prefix . $invoice->id,
                'name'                   => $invoice->membership->member->name ?? '-',
                'phone'                  => $invoice->membership->member->phone ?? '-',
                'membership'             => $invoice->membership->service_pricelist->name ?? '-',
                'fees'                   => $invoice->service_fee ?? '-',
                'discount'               => $invoice->discount ?? 0,
                'net_amount'             => $invoice->net_amount,
                'paid_amount'            => $invoice->payments_sum_amount,
                'rest_amount'            => $invoice->rest,
                'trainer_by'             => $invoice->membership->trainer->name ?? '-',
                'sales_by_id'            => $invoice->sales_by->name ?? '-',
                'status'                 => \App\Models\Invoice::STATUS_SELECT[$invoice->status],
                'created_by_id'          => $invoice->created_by->name ?? '-',
                'created_at'             => $invoice->created_at,
            ];
        });

        return $invoices;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Member Name',
            'Member Phone',
            'Membership',
            'Service Fees',
            'Discount',
            'Net Amount',
            'Paid Amount',
            'Rest',
            'Trainer By',
            'Sales By',
            'Status',
            'Created By',
            'Created At'
        ];
    }
}
