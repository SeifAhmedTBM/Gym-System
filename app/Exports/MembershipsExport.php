<?php

namespace App\Exports;

use App\Models\Membership;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MembershipsExport implements FromCollection,WithHeadings
{
    use Exportable;

    public function collection()
    {
        $memberships = Membership::index(request()->all())
                                    ->whereHas('member')
                                    ->with(['member','service_pricelist','attendances'])
                                    ->latest()
                                    ->get();

        $memberships = $memberships->map(function($membership){
            return [
                'code'                  => $membership->member->member_code,
                'name'                  => $membership->member->name,
                'phone'                 => $membership->member->phone,
                'start_date'            => $membership->start_date,
                'end_date'              => $membership->end_date,
                'trainer'               => $membership->trainer->name ?? '-',
                'service'               => $membership->service_pricelist->name ?? '-',
                'status'                => $membership->status,
                'remaining_sessions'    => $membership->service_pricelist->service->service_type->session_type == 'sessions' ? $membership->attendances()->count() . ' \\ ' . $membership->service_pricelist->session_count : '-',
                'sales_by_id'           => $membership->sales_by->name ?? '-',
                'created_at'            => $membership->created_at,
            ];
        });

        return $memberships;
    }

    public function headings(): array
    {
        return [
            'Member Code',
            'Member Name',
            'Member Phone',
            'Start Date',
            'End Date',
            'Trainer',
            'Service',
            'Status',
            'Remaining Sessions',
            'sales by',
            'created At'
        ];
    }
}
