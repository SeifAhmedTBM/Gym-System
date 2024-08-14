<?php

namespace App\Exports;

use App\Models\Membership;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExpiringExpiredExport implements FromCollection , WithHeadings
{
    public function collection()
    {
        $expiring_memberships = Membership::index(request()->all())
            ->withCount('attendances')
            ->withCount('trainer_attendances')
            ->with(['service_pricelist.service.service_type','member.branch','assigned_coach','sales_by'])
            ->whereIn('status',['expiring','expired'])
            ->latest()
            ->get()
            ->map(function($membership){
                return [    
                    'member_code'       => $membership->member->member_code ?? '-',
                    'name'              => $membership->member->name ?? '-',
                    'phone'             => $membership->member->phone ?? '-',
                    'pricelist'         => $membership->service_pricelist->name ?? '-',
                    'service'           => $membership->service_pricelist->service->name ?? '-',
                    'service_type'      => $membership->service_pricelist->service->service_type->name ?? '-',
                    'start_date'        => $membership->start_date ?? '-',
                    'end_date'          => $membership->end_date ?? '-',
                    'trainer'           => $membership->trainer->name ?? '-',
                    'sales_by'          => $membership->sales_by->name ?? '-',
                    'attendances'       => $membership->attendances_count ?? '-',
                    'created_at'        => $membership->created_at ?? '-',
                ];
            });

        return $expiring_memberships;
    }

    public function headings(): array
    {
        return [
            'Member code',
            'Member name',
            'Member phone',
            'Pricelist ',
            'Service',
            'Service Type',
            'Start date',
            'End date',
            'Trainer',
            'Sales by',
            'Attendances Count',
            'Created at'
        ];
    }
}
