<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TaxAccountantExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $from = request('from') != NULL ? request('from') : date('Y-m-01');

        $to = request('to') != NULL ? request('to') : date('Y-m-t');

        $branch_id = request('branch_id') != NULL ? request('branch_id') : NULL;

        $payments = Payment::index(request()->all())
                                ->with([
                                    'invoice',
                                    'invoice.membership',
                                    'invoice.membership.member',
                                    'invoice.membership.member.branch',
                                    'invoice.membership.service_pricelist',
                                    'account'
                                ])
                                ->whereHas('invoice',function($q){
                                    $q->where('status','!=','refund')
                                        ->whereHas('membership',function($x){
                                            $x->whereHas('service_pricelist');
                                        });
                                })
                                ->whereDate('created_at','>=',$from)
                                ->whereDate('created_at','<=',$to)
                                ->whereHas('account',fn($q) => $q
                                        ->where('name','NOT LIKE','%cash%')
                                        ->where('name','NOT LIKE','%vodafone%')
                                )
                                ->latest()
                                ->get();

        $payments = $payments->map(function ($payment) {
            return [
                'id'                        => $payment->id,
                'name'                      => $payment->invoice->membership->member->name ?? 'N/D',
                'national'                  => $payment->invoice->membership->member->national ?? 'N/D',
                'pricelist'                 => $payment->invoice->membership->service_pricelist->name ?? 'N/D',
                'amount'                    => number_format($payment->amount) ?? 'N/D',
                'account'                   => $payment->account->name ?? 'N/D',
                'branch'                    => $payment->invoice->membership->member->branch->name ?? 'N/D',
                'created_at'                => $payment->created_at ?? 'N/D',
            ];
        });

        return $payments;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Member',
            'National ID',
            'Pricelist',
            'Amount',
            'Account',
            'Branch',
            'Created At'
        ];
    }
}
