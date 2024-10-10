<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentsExport implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
        $employee = Auth()->user()->employee;

        $query = Payment::index(request()->all())
            ->with(['invoice', 'invoice.membership', 'invoice.membership.member', 'account', 'sales_by'])
            ->whereHas('invoice', function ($q) {
                $q->whereHas('membership')
                    ->where('status', '!=', 'refund');
            });

        if ($employee && $employee->branch_id != NULL) {
            $query->whereHas('account', function ($q) use ($employee) {
                $q->whereBranchId($employee->branch_id);
            });
        }

        $payments = $query->latest()->get();

        return $payments->map(function ($payment) {
            return [
                'id'            => $payment->id,
                'name'          => $payment->invoice->membership->member->name ?? '-',
                'account'       => $payment->account->name ?? '-',
                'service'       => $payment->invoice->membership->service_pricelist->name ?? '-',
                'amount'        => $payment->amount ?? '-',
                'invoice'       => Setting::first()->invoice_prefix . $payment->invoice_id ?? '-',
                'sales_by_id'   => $payment->sales_by->name ?? '-',
                'created_at'    => $payment->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Member Name',
            'Account',
            'Service',
            'Amount',
            'Invoice ID',
            'Sales By',
            'Created At'
        ];
    }
}
