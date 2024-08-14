<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportRevenueDetailsReport implements FromCollection, WithHeadings
{
    public $revenue;

    public function __construct($revenue)
    {
        $this->revenue = $revenue;
    }

    public function headings(): array
    {
        return [
            'Athlete',
            'Membership Details',
            'Sessions Cost',
            'Session Details',
            'Attended At'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = collect([]);
        foreach($this->revenue as $rev) {
            $memberships_details = "".trans('cruds.pricelist.title_singular')." : ";
            $memberships_details .= $rev->membership->service_pricelist->name . ' , ';

            $memberships_details .= trans('cruds.pricelist.fields.amount') . ' : ';
            $memberships_details .= $rev->membership->service_pricelist->amount . ' , ';

            $memberships_details .= trans('cruds.pricelist.fields.session_count') . ' : ';
            $memberships_details .= $rev->membership->service_pricelist->session_count;

            $data->push([
                'Athlete'                   =>  $rev->member->name,
                'Membership Details'        =>  $memberships_details,
                'Sessions Cost'             =>  !is_null($rev->membership->invoice) ? round(
                                                $rev->membership->invoice->net_amount / $rev->membership->service_pricelist->session_count
                                                ) . ' EGP' : 0,
                'Session Details'           => $rev->schedule->session->name . ' ( ' . date('g:i A', strtotime($rev->schedule->timeslot->from)) . ' ) ',
                'Attended At'               => $rev->created_at->format('Y-m-d , g:i:s A')
            ]);
        }
        return $data;
    }
}
