<?php

namespace App\Exports;

use App\Models\FreezeRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FreezesExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $freezes = FreezeRequest::index(request()->all())
                                    ->whereHas('membership')
                                    ->with(['membership' => fn($q) => $q->with('member')])
                                    ->whereStatus('confirmed')
                                    ->latest()
                                    ->get();

        $freezes = $freezes->map(function($freeze){
            return [
                    'id'                => $freeze->id,
                    'member_code'       => $freeze->membership->member->member_code ?? '-',
                    'name'              => $freeze->membership->member->name ?? '-',
                    'phone'             => $freeze->membership->member->phone ?? '-',
                    'membership'        => $freeze->membership->service_pricelist->name ?? '-',
                    'freeze'            => $freeze->freeze ?? '-',
                    'start_date'        => $freeze->start_date ?? '-',
                    'end_date'          => $freeze->end_date ?? '-',
                    'created_at'        => $freeze->created_at,
                ];
            });

        return $freezes;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Member Code',
            'Member Name',
            'Member Phone',
            'Membership',
            'Freeze',
            'Start Date',
            'End Date',
            'Created At'
        ];
    }
}
