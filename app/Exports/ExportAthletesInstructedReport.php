<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportAthletesInstructedReport implements FromCollection, WithHeadings
{

    public $athletes;

    public function __construct($athletes)
    {
        $this->athletes = $athletes;
    }

    public function headings(): array
    {
        return [
            'Member',
            'Attendances Count'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = collect([]);
        foreach($this->athletes as $athlete) {
            $data->push([
                'Member'                =>  $athlete->first()->member->name,
                'Attendances Count'     => $athlete->count()
            ]);
        }
        return $data;
    }
}
