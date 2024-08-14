<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportCoachesReport implements FromCollection , WithHeadings
{
    public $reports;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function headings(): array
    {
        return [
            'Revenue Rank',
            'Coach',
            'Sessions Instructed',
            'Athletes Instructed',
            'Revenue'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = collect([]);
        
        foreach($this->reports as $key => $report) {
            $data->push([
                'Revenue Rank'              => $key + 1,
                'Coach'                     => $report['trainer_name'],
                'Sessions Instructed'       => $report['sessions_instructed'],
                'Athletes Instructed'       => $report['athletes_instructed'],
                'Revenue'                   => $report['revenue'] . ' EGP'
            ]);
        }
        return $data;
    }
}
