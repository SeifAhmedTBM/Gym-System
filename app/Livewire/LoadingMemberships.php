<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Refund;
use Livewire\Component;
use App\Models\Membership;

class LoadingMemberships extends Component
{
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

        // check membership status
        if ($membership->start_date > date('Y-m-d')) 
        {
            $membership->update([
                'status'    => 'pending'
            ]);
        } elseif ($membership->end_date <= date('Y-m-d')) 
        {
            $membership->update([
                'status'    => 'expired'
            ]);
        } elseif ($membership->start_date <= date('Y-m-d') && $membership->end_date > date('Y-m-d')) {
            // check type of service type
           
            if ($membership->service_pricelist->service->service_type->session_type == 'non_sessions') // Non Sessions
            {
                $remaining_non_session = now()->diffInDays(Carbon::parse($membership->end_date), false);

                // check differance between end date & today
                if ($remaining_non_session <= $membership->service_pricelist->expiring_date) {
                    $membership->update([
                        'status'    => 'expiring'
                    ]);
                } else {
                    $membership->update([
                        'status'    => 'current'
                    ]);
                }
            } else // Session & Group Session
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

        $refunds = Refund::whereStatus('confirmed')
            ->with([
                'invoice',
                'invoice.membership',
            ])
            ->whereHas('invoice', function ($q) {
                $q->whereHas('membership');
            })
            ->get();

        foreach ($refunds as $key => $refund) {
            $refund->invoice->membership->update([
                'status'    => 'refunded'
            ]);

            $refund->invoice->update([
                'status'    => 'refund'
            ]);
        }
    }

    public function refresh()
    {
        // $memberships = Membership::whereHas('attendances')
        //                                 ->with('attendances')
        //                                 ->withCount('attendances')
        //                                 ->get();
        $memberships = Membership::with('attendances')
            ->withCount('attendances')
            ->get();

        foreach ($memberships as $membership) 
        {
            if ($membership->attendances_count > 0) 
            {
                $membership->update([
                    'last_attendance'  => $membership->attendances ? $membership->attendances->last()->created_at : NULL,
                ]);
            }

            $this->adjustMembership($membership);
        }
    }

    public function render()
    {
        return view('livewire.loading-memberships');
    }
}
