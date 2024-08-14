<?php

namespace App\Exports;

use App\Models\MembershipAttendance;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportExpiredMembershipAttendancesReport implements FromCollection, WithHeadings
{

    public function headings(): array
    {
        return [
            'ID',
            'Member Code',
            'Member',
            'Member Phone',
            'Membership',
            'Trainer',
            'Sign In',
            'Sign Out',
            'Created At'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $membershipsAttendances = MembershipAttendance::index(request()->except('_token'))
                                    ->whereHas('membership',function($q){
                                        $q->whereHas('member');
                                    })
                                    ->whereMembershipStatus('expired')
                                    ->with(['membership' => fn($q) =>$q->with(['member','service_pricelist']) ])
                                    ->latest()
                                    ->get();

        $membershipsAttendances = $membershipsAttendances->map(function($attend){
            return [
                'id'                => $attend->id,
                'member_code'       => $attend->membership->member->member_code ?? '-',
                'member'            => $attend->membership->member->name ?? '-',
                'phone'             => $attend->membership->member->phone ?? '-',
                'membership'        => $attend->membership->service_pricelist->name ?? '-',
                'trainer'           => $attend->membership->trainer->name ?? '-',
                'sign_in'           => $attend->sign_in,
                'sign_out'          => $attend->sign_out,
                'created_at'        => $attend->created_at,
            ];
        });

        return $membershipsAttendances;
    }
}
