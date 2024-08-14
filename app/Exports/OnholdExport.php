<?php

namespace App\Exports;

use App\Models\Setting;
use App\Models\Membership;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OnholdExport implements FromCollection,WithHeadings
{
    use Exportable;
    
    public function collection()
    {
        $setting = Setting::first()->inactive_members_days ?? 7;
        $memberships = Membership::with(['member',
                                        'service_pricelist',
                                        'service_pricelist.service'
                                        ])
                                    ->whereHas('member')
                                    ->whereDate('last_attendance','<',date('Y-m-d',strtotime('-'. $setting .'Days')))
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
                'membership'             => $membership->service_pricelist->name ?? '-',
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
            'Membership',
            'Sales By',
            'Created At'
        ];
    }
}
