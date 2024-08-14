<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class GuestLogExport implements FromCollection,WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $from   = request('from') ? request('from') : date('Y-m-01');
        $to     = request('to')? request('to') : date('Y-m-t');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = request('branch_id') != NULL ? request('branch_id') : '';
        }

        $latest_leads = Lead::with(['memberships.service_pricelist', 'invoices.payments', 'source','sales_by','branch'])
            ->when($branch_id, fn($q) => $q->whereBranchId(request('branch_id')))
            ->when(request('source_id'), fn($q) => $q->whereSourceId(request('source_id')))
            ->when(request('sales_by_id'), fn($q) => $q->whereSalesById(request('sales_by_id')))
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<',$to)
            ->latest()
            ->get()
            ->map(function($lead) {
                return [
                    'member_code'           => $lead->member_code ?? '-',
                    'member_name'           => $lead->name,
                    'member_phone'          => $lead->phone,
                    'type'                  => Lead::TYPE_SELECT[$lead->type],
                    'sales_by'              => $lead->sales_by->name ?? '-',
                    'source'                => $lead->source->name ?? '-',
                    'branch'                => $lead->branch->name ?? '-',
                    'membership'            => $lead->last_membership ? $lead->last_membership->service_pricelist->name : '-',
                    'paid'                  => $lead->last_membership ? number_format($lead->last_membership->invoice->net_amount ?? 0) : '0',
                    'paid'                  => $lead->last_membership ? number_format($lead->last_membership->invoice->rest ?? 0) : '0',
                ];
            });

        return $latest_leads;
    }

    public function headings(): array
    {
        return [
            'Member Code',
            'Name',
            'Phone',
            'Type',
            'Sales By',
            'Source',
            'Branch',
            'Membership',
            'Paid',
            'Rest',
        ];
    }
}
