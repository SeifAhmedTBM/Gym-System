<?php

namespace App\Exports;

use App\Models\Setting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportSessionAttendancesReport implements FromCollection,WithHeadings
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $report = collect([]);
        foreach ($this->data as $attendance) {
            foreach($attendance['attendants'] as $attendant) {
                $report->push([
                    'Member Code'   => Setting::first()->member_prefix ? Setting::first()->member_prefix . $attendant['member_code'] : $attendant['member_code'],
                    'Member Name'   => $attendant['member_name'],
                    'Member Phone'  => $attendant['member_phone'],
                    'Gender'        => $attendant['gender'],
                    'Session List'  => $attendance['session'],
                    'Trainer'       => $attendance['schedule_trainer'],
                    'Time'          => $attendance['timeslot'],
                ]);
            }
        }

        return $report;
    }

    public function headings(): array
    {
        return [
            'Member Code',
            'Member Name',
            'Member Phone',
            'Gender',
            'Session List',
            'Trainer',
            'Time'
        ];
    }
}
