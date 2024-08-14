<?php

namespace App\Exports;

use App\Models\Setting;
use App\Models\Membership;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ActiveMembersExport implements FromCollection,WithHeadings
{
    use Exportable;

    public function collection()
    {
        $setting = Setting::first()->inactive_members_days ?? 7;

        $branch_id = request('branch_id') ?? NULL;

        $memberships = Membership::with([
                                    'service_pricelist',
                                    'service_pricelist.service',
                                    'invoice',
                                    'member.branch'
                                ])
                                ->whereHas('member',fn($q) => $q->when($branch_id,fn($y) => $y->whereBranchId($branch_id)))
                                // ->whereHas('attendances')
                                ->whereDate('last_attendance','>=',date('Y-m-d',strtotime('-'. $setting .'Days')))
                                ->whereIn('status',['current','expiring'])
                                ->whereHas('service_pricelist',function($y){
                                    $y->whereHas('service',function($b){
                                        $b->whereHas('service_type',function($x){
                                            $x->whereMainService(true);
                                        });
                                    });
                                })
                                ->latest()
                                ->get();

        $memberships = $memberships->map(function ($membership) {
            return [
                'id'                     => $membership->id,
                'member_code'            => $membership->member->member_code ?? '-',
                'name'                   => $membership->member->name ?? '-',
                'phone'                  => $membership->member->phone ?? '-',
                'last_attendance'        => $membership->attendances[0]->created_at ?? 'No Attendance',
                'membership'             => $membership->service_pricelist->name ?? '-',
                'branch'                 => $membership->member->branch->name ?? '-',
                'sales_by_id'            => $membership->sales_by->name ?? '-',
                'created_at'             => $membership->created_at,
            ];
        });

        return $memberships;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Member Code',
            'Name',
            'Phone',
            'Last Attendance',
            'Membership',
            'Branch',
            'Sales By',
            'Created At'
        ];
    }
}
