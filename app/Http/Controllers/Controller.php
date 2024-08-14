<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Refund;
use App\Models\Setting;
use App\Models\Reminder;
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
        // Alert::error(NULL,trans('global.cannot_attend'),'Cannot take attend now !');
        Alert::error(NULL,'Attended Already !');
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

    public function welcome_call($membership)
    { 
        $membership = Membership::with(['member'])->find($membership->id);

        $membership->reminders()->whereType('welcome_call')->delete();

        $reminder_start_date    = Carbon::parse($membership->start_date);
        $reminder_end_date      = Carbon::parse($membership->end_date);
        $reminder_days          = $reminder_end_date->diffInDays($reminder_start_date);
        $reminder_quarter       = ceil($reminder_days / 4);

        // first welcome call 0%
        Reminder::create([
            'type'              => 'welcome_call',
            'membership_id'     => $membership->id,
            'lead_id'           => $membership->member_id,
            'due_date'          => date("Y-m-d", strtotime("+1 day")),
            'user_id'           => $membership->member->sales_by_id,
        ]);

        // 25% 
        Reminder::create([
            'type'              => 'welcome_call',
            'membership_id'     => $membership->id,
            'lead_id'           => $membership->member_id,
            'due_date'          => date("Y-m-d", strtotime("+ ".$reminder_quarter." day")),
            'user_id'           => $membership->member->sales_by_id,
        ]);

        // 50% 
        Reminder::create([
            'type'              => 'welcome_call',
            'membership_id'     => $membership->id,
            'lead_id'           => $membership->member_id,
            'due_date'          => date("Y-m-d", strtotime("+ ".($reminder_quarter*2)." day")),
            'user_id'           => $membership->member->sales_by_id,
        ]);

        // 75% 
        Reminder::create([
            'type'              => 'welcome_call',
            'membership_id'     => $membership->id,
            'lead_id'           => $membership->member_id,
            'due_date'          => date("Y-m-d", strtotime("+ ".($reminder_quarter*3)." day")),
            'user_id'           => $membership->member->sales_by_id,
        ]);
    }

    public function renew_call($membership)
    {
        $membership = Membership::with(['member'])->find($membership->id);

        $membership->reminders()->whereType('renew')->delete();

        $reminder_end_date      = Carbon::parse($membership->end_date);
        
        // renew call
        Reminder::create([
            'type'              => 'renew',
            'membership_id'     => $membership->id,
            'lead_id'           => $membership->member_id,
            'due_date'          => date("Y-m-d", strtotime($reminder_end_date." -5 day")),
            'user_id'           => $membership->member->sales_by_id,
        ]);

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
        
        // return dd($membership->end_date > date('Y-m-d'));
        // return dd($membership->start_date,$membership->end_date);
        // check membership status
        if ($membership->start_date > date('Y-m-d')) 
        {
            $membership->update([
                'status'    => 'pending'
            ]);
        }elseif ($membership->end_date <= date('Y-m-d')) 
        {
            $membership->update([
                'status'    => 'expired'
            ]);
        }elseif ($membership->start_date <= date('Y-m-d') && $membership->end_date > date('Y-m-d')) 
        {
            // check type of service type
            if ($membership->service_pricelist->service->service_type->session_type == 'non_sessions') // Non Sessions
            {
                $remaining_non_session = now()->diffInDays(Carbon::parse($membership->end_date),false);
                
                // check differance between end date & today
                if ($remaining_non_session <= $membership->service_pricelist->expiring_date) 
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
            else // Session & Group Session
            {
                $remaining = ($membership->service_pricelist->session_count - $membership->trainer_attendances_count);
                $remaining_membership = now()->diffInDays(Carbon::parse($membership->end_date),false);

                if ($remaining <= 0) 
                {
                    $membership->update([
                        'status'    => 'expired'
                    ]);
                } elseif (($remaining < $membership->service_pricelist->expiring_session) || $remaining_membership <= $membership->service_pricelist->expiring_date) 
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
        }

        // refund
        $refunds = Refund::whereStatus('confirmed')
                        ->with([
                            'invoice',
                            'invoice.membership',
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
