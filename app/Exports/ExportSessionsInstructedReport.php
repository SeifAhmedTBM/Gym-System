<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportSessionsInstructedReport implements FromCollection , WithHeadings
{

    public $sessions;

    public function __construct($sessions)
    {
        $this->sessions = $sessions;
    }

    public function headings() : array
    {
        return [
            'Trainer',
            'Session',
            'Time',
            'Date'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = collect([]);
        foreach($this->sessions as $session) {
            $data->push([
                'Trainer'       => $session->trainer->name,
                'Session'       => $session->schedule->session->name,
                'Time'          => date('g:i A', strtotime($session->schedule->timeslot->from)),
                'Date'          => $session->day
            ]);
        }
        return $data;
    }
}
