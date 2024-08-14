<?php

namespace App\Jobs;

use App\Models\Refund;
use App\Models\Invoice;
use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpdatingMemberships implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        // updating memberships statuses
        $memberships = Membership::with([
                        'service_pricelist', 
                        'service_pricelist.service', 
                        'service_pricelist.service.service_type', 
                        'invoice'
                    ])
                    ->whereHas('service_pricelist',function($q){
                        $q->whereHas('service',function($x){
                            $x->whereHas('service_type');
                        });
                    })
                    ->whereHas('invoice')
                    ->where('status','!=','refunded')
                    ->get();

        

        foreach ($memberships as $key => $membership) 
        {
            if ($membership->start_date > date('Y-m-d')) 
            {
                $membership->update([
                    'status'    => 'pending'
                ]);

            }elseif($membership->start_date <= date('Y-m-d') && $membership->end_date > date('Y-m-d'))
            {

                if (($membership->service_pricelist->service->service_type->session_type == 'sessions') || ($membership->service_pricelist->service->service_type->session_type == 'group_sessions')) 
                {
                    $diff = $membership->service_pricelist->session_count - $membership->attendances->count();

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
