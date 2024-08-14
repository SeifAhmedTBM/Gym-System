<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\Setting;
use App\Models\Membership;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
    
    public function deleted()
    {
        Alert::success(NULL, trans('global.deleted_successfully'));
    }

    public function created()
    {
        Alert::success(NULL, trans('global.created_successfully'));
    }

    public function loggedin()
    {
        Alert::success(NULL, trans('global.loggedin_successfully'));
    }

    public function updated()
    {
        Alert::success(NULL, trans('global.updated_successfully'));
    }

    public function transfered()
    {
        Alert::success(NULL, trans('global.transfered_successfully'));
    }

    public function sent_successfully()
    {
        Alert::success(NULL, trans('global.sent_successfully'));
    }

    public function migrated()
    {
        Alert::success(NULL, trans('global.migrated_successfully'));
    }

    public function something_wrong()
    {
        Alert::error(NULL, trans('global.something_wrong'), 'Please check account balance');
    }

    public function duplicated_member_code()
    {
        Alert::error(NULL, trans('global.something_wrong'), 'Duplicated Member Code !');
    }

    public function no_data()
    {
        Alert::error(NULL, 'there is no data avaiable !', 'Please check account balance');
    }

    public function wrong_employee_card()
    {
        Alert::error(NULL, trans('global.something_wrong'), 'This card number is not correct !');
    }

    public function please_check()
    {
        Alert::error(NULL, trans('global.something_wrong'),'Please Check Time of service');
    }

    public function cannotAttend()
    {
        Alert::error(NULL,trans('global.cannot_attend'),'Cannot take attend now !');
    }

    public function branchNotFound()
    {
        Alert::error(NULL,trans('global.branch_not_found'),'You Not Assigned to Branch !');
    }

    public function refunded_membership()
    {
        Alert::error(NULL, trans('global.refunded_membership'), 'Membership is refunded');
    }

    public function expired_membership()
    {
        Alert::error(NULL, trans('global.expired_membership'), 'Membership is expired');
    }

    public function employee_schedule()
    {
        Alert::error(NULL, trans('global.employee_schedule'), 'Please enter data into Employee Schedule');
    }

    public function adjustMembership($membership)
    {
            $membership = Membership::with([
                                    'service_pricelist',
                                    'service_pricelist.service',
                                    'service_pricelist.service.service_type'
                                ])
                                ->withCount('attendances')
                                ->withCount('trainer_attendances')
                                ->findOrFail($membership->id);
            
            if ($membership->start_date > date('Y-m-d')) 
            {
                $membership->update([
                    'status'    => 'pending'
                ]);
            }elseif($membership->start_date <= date('Y-m-d') && $membership->end_date > date('Y-m-d'))
            {
                if (($membership->service_pricelist->service->service_type->session_type == 'sessions') || ($membership->service_pricelist->service->service_type->session_type == 'group_sessions')) 
                {
                    // $diff = $membership->service_pricelist->session_count - $membership->attendances_count;
                    $diff = $membership->service_pricelist->session_count - $membership->trainer_attendances_count;

                    if ($diff <= 0) 
                    {
                        $membership->update([
                            'status'    => 'expired'
                        ]);
                    } elseif ($diff < $membership->service_pricelist->expiring_session) 
                    {
                        $membership->update([
                            'status'    => 'expiring'
                        ]);
                    } else {
                        $membership->update([
                            'status'    => 'current'
                        ]);
                    }

                } else {
                    $date1 = date('Y-m-d');
                    $date2 = $membership->end_date;
                    $diff = abs(strtotime($date2) - strtotime($date1));
                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                    $diff = ($years * 365) + ($months * 30) + $days;
                    
                    if ($diff <= $membership->service_pricelist->expiring_date) 
                    {
                        $membership->update([
                            'status'    => 'expiring'
                        ]);
                    } else {
                        $membership->update([
                            'status'    => 'current'
                        ]);
                    }
                }
            }elseif ($membership->end_date <= date('Y-m-d')) 
            {
                $membership->update([
                    'status'    => 'expired'
                ]);
            }

            $refunds = Refund::whereStatus('confirmed')
                            ->with([
                                'invoice' => fn($q) => $q->with('membership')
                            ])
                            ->whereHas('invoice',function($q){
                                $q->whereHas('membership');
                            })
                            ->get();

            foreach ($refunds as $key => $refund) 
            {
                $refund->invoice->membership->update([
                    'status'    => 'refunded'
                ]);

                $refund->invoice->update([
                    'status'    => 'refund'
                ]);
            }
    }
    
}
