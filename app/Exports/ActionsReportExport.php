<?php

namespace App\Exports;

use App\Models\Lead;
use App\Models\Reminder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ActionsReportExport implements FromCollection,WithHeadings
{
    use Exportable;

    public function collection()
    {
        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) 
        {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = request('branch_id') != NULL ? request('branch_id') : '';
        }

        $from = request('from') ? request('from') : date('Y-m-01');
        $to = request('to') ? request('to') : date('Y-m-t');

        $reminder_actions   = Reminder::with([
                    'lead' => fn ($q) => $q->with(['source', 'branch']),
                    'membership' => fn ($q) => $q->with([
                        'invoice'           => fn ($q) => $q->withSum('payments', 'amount'),
                        'service_pricelist'
                    ]),
                    'user'
                ])
                ->whereHas(
                    'lead',
                    fn ($q) => $q
                        ->when(request('type'), fn ($y) => $y->whereType(request('type')))
                        ->when($branch_id, fn ($q) => $q->whereBranchId($branch_id))
                )
                ->when(request('sales_by_id'), fn ($q) => $q->whereUserId(request('sales_by_id')))
                ->when(request('reminder_action'), fn ($q) => $q->whereAction(request('reminder_action')))
                ->whereNotIn('type',['pt_session'])
                ->whereDate('due_date', '>=', $from)
                ->whereDate('due_date', '<=', $to)
                ->get()
                ->map(function($reminder){
                    return [
                        'member_code'       => $reminder->lead->member_code ?? '-',
                        'member_name'       => $reminder->lead->name ?? '-',
                        'member_phone'      => $reminder->lead->phone ?? '-',
                        'type'              => Lead::TYPE_SELECT[$reminder->lead->type] ?? '-',
                        'branch'            => $reminder->lead->branch->name ?? '-',
                        'reminder_type'     => Reminder::TYPE[$reminder->type] ?? '-',
                        'action'            => Reminder::ACTION[$reminder->action] ?? '-',
                        'membership'        => $reminder->membership->service_pricelist->name ?? '-',
                        'due_date'          => $reminder->due_date ?? '-',
                        'sales_by'          => $reminder->user->name ?? '-',
                        'notes'             => $reminder->notes ?? '-',
                        'created_at'        => $reminder->created_at ?? '-',
                    ];
                });

        return $reminder_actions;
    }

    public function headings(): array
    {
        return [
            'Member Code',
            'Name',
            'Phone',
            'Type',
            'Branch',
            'Reminder Type',
            'Action',
            'Membership',
            'Due Date',
            'Sales By',
            'Notes',
            'Action Date',
        ];
    }
}
