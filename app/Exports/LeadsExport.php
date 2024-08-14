<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadsExport implements FromCollection,WithHeadings
{
    use Exportable;

    public function collection()
    {   
        if(auth()->user()->roles[0]->title == 'Sales'){
            $leads = Lead::index(request()->all())
            ->whereType('lead')
            ->where('sales_by_id',Auth()->user()->id)
            ->with(['status','source','sales_by','address'])
            ->latest()->get();
        }else{
            $leads = Lead::index(request()->all())
            ->whereType('lead')
            ->with(['status','source','sales_by','address'])
            ->latest()->get();
        }
       

        $leads = $leads->map(function($lead){
            return [
                'id'              => $lead->id,
                'name'            => $lead->name,
                'phone'           => $lead->phone,
                'parent_phone'    => $lead->parent_phone,
                'parent_phone_two'=> $lead->parent_phone_two,
                'address'         => $lead->address->name ?? '-',
                'status'          => $lead->status->name ?? '-',
                'source'          => $lead->source->name ?? '-',
                'gender'          => \App\Models\Lead::GENDER_SELECT[$lead->gender],
                'sales_by_id'     => $lead->sales_by->name ?? '-',
                'created_at'      => $lead->created_at,
            ];
        });

        return $leads;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Phone',
            'Parent Phone',
            'Parent Phone 2',
            'Address',
            'Status',
            'Source',
            'Gender',
            'Sales by',
            'Created At'
        ];
    }
}
