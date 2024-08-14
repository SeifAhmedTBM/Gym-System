<?php

namespace App\Exports;

use App\Models\Setting;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FreezeRequestsExport implements FromCollection, WithHeadings
{

    public $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function headings(): array
    {
        return [
            'Member Code',
            'Member Name',
            'Member Phone',
            'Freeze',
            'Start Date',
            'End Date',
            'Consumed',
            'Status',
            'Created By',
            'Created At'
        ];
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = collect([]);
        $settings = Setting::first();
        foreach($this->report as $report) {
            $now = Carbon::now();
            $start_date = Carbon::parse($report->start_date);
            $data->push([
                'Member Code'       => $settings->member_prefix . $report->membership->member->member_code,
                'Member Name'       => $report->membership->member->name,
                'Member Phone'      => $report->membership->member->phone,
                'Freeze'            => $report->freeze,
                'Start Date'        => $report->start_date,
                'End Date'          => $report->end_date,
                'Consumed'          => $now->diffInDays($start_date),
                'Status'            => ucfirst($report->status),
                'Created By'        => $report->created_by->name,
                'Created At'        => $report->created_at->toFormattedDateString()
            ]);
        }

        return $data;
    }
}
