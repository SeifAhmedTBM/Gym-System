<?php

namespace App\Http\Helpers;

use App\Models\User;


class Trainer {

    public static function collected($trainer_id, $date_from = NULL, $date_to = NULL)
    {
        $trainer = User::find($trainer_id);
        $from = ($date_from == NULL ? date('Y-m-01') : $date_from);
        $to = ($date_to == NULL ? date('Y-m-t') : $date_to);
        $collected = 0;
        $pre_collected = 0;
        $response = [];
        $memberships = $trainer->trainer_memberships()->whereBetween('start_date', [date('Y-m-d', strtotime($from)) , date('Y-m-d', strtotime($to))])->get();
        $pre_memberships = $trainer->trainer_memberships()->whereMonth('start_date', date('m'))->whereDate('start_date', '<', $from)->get();
        foreach($memberships as $membership) {
            $membership_amount = $membership->invoice->net_amount;
            $attendance_count = $membership->attendances()->whereBetween('created_at', [date('Y-m-d', strtotime($from)) , date('Y-m-d', strtotime($to))])->count();
            $memberships_sessions = $membership->service_pricelist->session_count;
            $session_price =  $membership_amount/$memberships_sessions;
            $collected += ($session_price*$attendance_count);
        }
        foreach($pre_memberships as $pre_membership) {
            $pre_membership_amount = $pre_membership->invoice->net_amount;
            $pre_attendance_count = $pre_membership->attendances()->whereBetween('created_at', [date('Y-m-d', strtotime($from)) , date('Y-m-d', strtotime($to))])->count();
            $pre_memberships_sessions = $pre_membership->service_pricelist->session_count;
            $session_price =  $pre_membership_amount/$pre_memberships_sessions;
            $pre_collected += ($session_price*$pre_attendance_count);
        }
        array_push($response, [
            'trainer_name'              => $trainer->name,
            'number_of_memberships'     => $memberships->count(),
            'collected'                 => $collected,
            'pre_collected'             => $pre_collected,
            'this_month_percentage'     => self::getCommissionPercentage($trainer, date('Y-m', strtotime($from)), $collected)
        ]);

        return array_values($response);
    }


    // public static function getCommissionPercentage($trainer, $month, $collected)
    // {
    //     $target = $trainer->employee->target_amount;
    //     if($collected == 0) {
    //         $achievement = round($target / 1);
    //     }else {
    //         $achievement = round($target / $collected);
    //     }
    //     $commission = $trainer->sales_tier->sales_tier()->where('month', $month)->first()->sales_tiers_ranges()->where('range_from','<=', $achievement)->first()->commission;
    //     return $commission;
    // }


}