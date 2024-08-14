<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentsExport implements FromCollection,WithHeadings
{
    use Exportable;

    public function collection()
    {
        $payments = Payment::index(request()->all())
                            ->with(['invoice','invoice.membership','invoice.membership.member','account', 'sales_by'])
                            ->whereHas('invoice', fn($q) => $q->whereHas('membership'))
                            ->latest()
                            ->get();

        $payments = $payments->map(function ($payment) {
            return [
                'id'                     => $payment->id,
                'name'                   => $payment->invoice->membership->member->name ?? '-',
                'account'                => $payment->account->name ?? '-',
                'service'                => $payment->invoice->membership->service_pricelist->name ?? '-',
                'amount'                 => $payment->amount ?? '-',
                'invoice'                => \App\Models\Setting::first()->invoice_prefix.$payment->invoice_id ?? '-',
                'sales_by_id'            => $payment->sales_by->name ?? '-',
                'created_at'             => $payment->created_at,
            ];
        });

        return $payments;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Member Name',
            'Account',
            'service',
            'Amount',
            'Invoice ID',
            'Sales By',
            'Created At'
        ];
    }
}
