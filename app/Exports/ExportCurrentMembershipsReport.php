<?php

namespace App\Exports;

use App\Models\Setting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportCurrentMembershipsReport implements FromCollection, WithHeadings
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Member Code',
            'Member Name',
            'Member Phone',
            'Gender',
            'Start Date',
            'End Date',
            'Membership Status',
            'Service',
            'Service Type',
            'Sales By'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $report = collect([]);

        foreach($this->data as $rep) {
            $report->push([
                'Member Code'   => Setting::first()->member_prefix . $rep->member->member_code,
                'Member Name'   => $rep->member->name,
                'Member Phone'  => $rep->member->phone,
                'Gender'        => ucfirst($rep->member->gender),
                'Start Date'    => $rep->start_date,
                'End Date'      => $rep->end_date,
                'Status'        => ucfirst($rep->membership_status),
                'Service'       => $rep->service_pricelist->service->name,
                'Service Type'  => $rep->service_pricelist->service->service_type->name,
                'Sales By'      => $rep->sales_by->name
            ]);
        }

        return $report;
    }
}
