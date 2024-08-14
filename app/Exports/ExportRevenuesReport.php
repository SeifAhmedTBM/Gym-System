<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportRevenuesReport implements FromCollection, WithHeadings
{
    public $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function headings(): array
    {
        return [
            'Session',
            'Max Capacity',
            'Session Count',
            'Attended',
            'Revenue',
            'Utilization Rate'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = collect([]);
        foreach(collect($this->report)->sortByDesc('utilization_rate') as $rep) {
            $data->push([
                'Session'               =>  $rep['session']['name'],
                'Max Capacity'          =>  $rep['session']['max_capacity'],
                'Session Count'         =>  $rep['sessions_count'],
                'Attended'              =>  $rep['attendants'],
                'Revenue'               =>  round($rep['revenue']) . ' EGP',
                'Utilization Rate'      =>  $rep['utilization_rate']
            ]);
        }
        return $data;
    }
}
