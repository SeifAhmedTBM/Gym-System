<?php

namespace App\Exports;

use App\Models\Lead;
use App\Models\Service;
use App\Models\Pricelist;
use App\Models\Membership;
use App\Models\ServiceType;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PtExpiredMembershipsExport implements FromCollection, WithHeadings
{

    public function collection()
    {
        $current_members = Lead::whereHas('memberships', fn ($q) => $q->whereIn('status', ['current', 'expiring', 'pending']))
            ->get();

        $expired_has_current_members = Lead::index(request()->all())
                    ->whereType('member')
                    ->whereHas(
                        'memberships',fn($q) => $q
                            ->whereStatus('expired')
                            ->whereHas(
                                'service_pricelist.service.service_type',fn($y) => $y->whereIsPt(true)
                            )
                    )
                    ->with([
                        'branch',
                        'memberships' => fn ($q) => $q
                                ->whereHas('service_pricelist.service.service_type',fn($y) => $y->whereIsPt(true))
                                ->with([
                                    'trainer', 'sales_by','service_pricelist.service.service_type'
                                ])->orderBy('end_date','DESC'),
                    ])
                    ->withCount([
                        'memberships' => fn ($q) => $q
                                ->whereHas('service_pricelist.service.service_type', fn ($y) =>   $y->whereIsPt(true))
                    ])
                    ->latest()
                    ->get();

        $members = $expired_has_current_members->diff($current_members);
        $members = $members->map(function ($member) {
            return [
                'member_code'       => $member->member_code,
                'name'              => $member->name,
                'phone'             => $member->phone,
                'branch_name'       => $member->branch->name ?? '-',
                'membership'        => $member->memberships->first()->service_pricelist->name ?? '-',
                'service_type'      => $member->memberships->first()->service_pricelist->service->service_type->name ?? '-',
                'start_date'        => $member->memberships->first()->start_date ?? '-',
                'end_date'          => $member->memberships->first()->end_date ?? '-',
                'trainer'           => $member->memberships->first()->trainer->name ?? '-',
                'sales_by'          => $member->memberships->first()->sales_by->name ?? '-',
                'created_at'        => $member->memberships->first()->created_at
            ];
        });

        return $members;
    }

    public function headings(): array
    {
        return [
            'Code',
            'Member',
            'Phone',
            'Branch',
            'Membership',
            'Service type',
            'Start Date',
            'End Date',
            'Trainer',
            'Sales By',
            'Created at'
        ];
    }
}
