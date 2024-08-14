<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MembersExport implements FromCollection,WithHeadings
{
    use Exportable;

    public function collection()
    {
        if(auth()->user()->roles[0]->title == 'Sales'){
            $members = Lead::index(request()->all())
            ->whereType('member')
            ->where('sales_by_id',Auth()->user()->id)
            ->with(['status','source','sales_by','address'])
            ->latest()->get();
        }else{
            $members = Lead::index(request()->all())
            ->whereType('member')
            ->with(['status','source','sales_by','address'])
            ->latest()->get();
        }

        $members = $members->map(function($member){
            return [
                'id'              => $member->id,
                'member_code'     => $member->member_code,
                'name'            => $member->name,
                'phone'           => $member->phone,
                'address'         => $member->address->name ?? '-',
                'status'          => $member->status->name ?? '-',
                'source'          => $member->source->name ?? '-',
                'gender'          => \App\Models\Lead::GENDER_SELECT[$member->gender],
                'sales_by_id'     => $member->sales_by->name ?? '-',
                'created_at'      => $member->created_at,
            ];
        });

        return $members;
    }

    public function headings(): array
    {
        return [
            'ID',
            'member_code',
            'Name',
            'Phone',
            'Address',
            'Status',
            'Source',
            'Gender',
            'Sales by',
            'Created At'
        ];
    }
}
