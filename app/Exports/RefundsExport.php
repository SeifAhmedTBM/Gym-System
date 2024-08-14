<?php

namespace App\Exports;

use App\Models\Refund;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RefundsExport implements FromCollection,WithHeadings
{
    use Exportable;

    public function collection()
    {
        $refunds = Refund::index(request()->all())
                            ->with(['refund_reason', 'invoice', 'created_by','account'])
                            ->latest()
                            ->get();

        $refunds = $refunds->map(function ($refund) {
            return [
                'id'                     => $refund->id,
                'refund_reason'          => $refund->refund_reason->name ?? '-',
                'invoice'                => \App\Models\Setting::first()->invoice_prefix.$refund->invoice_id,
                'account'                => $refund->account->name ?? '-',
                'amount'                 => $refund->amount,
                'created_by'             => $refund->created_by->name ?? '-',
                'created_at'             => $refund->created_at,
            ];
        });

        return $refunds;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Refund Reason',
            'Invoice',
            'Account',
            'Amount',
            'Created By',
            'Created At'
        ];
    }
}
