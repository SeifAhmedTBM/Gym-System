<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportTrainersReport implements FromCollection, WithHeadings
{
    public $trainers;
    public $commission;
    public $pre_commission;

    public function headings() : array
    {
        return [
            'Name',
            'This month collected',
            'Previous month collected',
            'This month commission',
            'Previous month commissions',
            'Total commissions'
        ];
    }

    public function __construct($trainers, $commission, $pre_commission)
    {
        $this->trainers = $trainers;
        $this->commission = $commission;
        $this->pre_commission = $pre_commission;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = collect([]);
        foreach($this->trainers as $trainer_id => $trainer_name) {
            $pre_com = $this->pre_commission->where('trainer_id', $trainer_id)->first();
            $data->push([
                'Name'                                  => $trainer_name,
                'This month collected'                  => round($this->commission->where('trainer_id', $trainer_id)
                                                                      ->first()['total']) . ' EGP',
                'Previous month collected'              => (isset($pre_com) && $pre_com != NULL ? round($pre_com['pre_total']) : 0) . ' EGP',
                'This month commission'                 => $this->commission->where('trainer_id', $trainer_id)->first()['commission'] != 0 ? round(intval($this->commission->where('trainer_id', $trainer_id)->first()['commission'])) . ' EGP' : '0 EGP ',
                'Previous month commissions'            => isset($pre_com) && $pre_com != NULL ? round($pre_com['previous_months_commissions']) . ' EGP' : 0  . ' EGP',
                'Total commissions'                     => (round(intval($this->commission->where('trainer_id', $trainer_id)->first()['commission'])) + (isset($pre_com['previous_months_commissions']) ? round($pre_com['previous_months_commissions']) : 0)) . ' EGP'
            ]);
        }

        return $data;
    }
}
