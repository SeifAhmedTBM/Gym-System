<?php

namespace App\Exports;

use App\Models\Reminder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class RemindersExport implements FromCollection,WithHeadings
{
    use Exportable;

    public function collection()
    {
        $date = request('date') ?? date('Y-m-d');
        
        $reminders = Reminder::index(request()->all())
                                ->with(['lead','membership.service_pricelist','user'])
                                ->whereHas('lead')
                                ->whereDate('due_date',$date)
                                ->orderBy('due_date','desc')
                                ->get();

        $reminders = $reminders->map(function ($reminder) {
            return [
                'id'                        => $reminder->id,
                'member_code'               => $reminder->lead->member_code ?? '-',
                'lead_name'                 => $reminder->lead->name ?? '-',
                'lead_phone'                => $reminder->lead->phone ?? '-',
                'type'                      => $reminder->type ?? '-',
                'details'                   => $reminder->membership->service_pricelist->name ?? '-',
                'due_date'                  => $reminder->due_date ?? '-',
                'user'                      => $reminder->user->name ?? '-',
                'action_date'               => $reminder->action_date ?? '-',
                'created_at'                => $reminder->created_at ?? '-',
            ];
        });

        return $reminders;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Member code',
            'Name',
            'Phone',
            'Type',
            'Details',
            'Due Date',
            'User',
            'Action Date',
            'Created At'
        ];
    }
}
