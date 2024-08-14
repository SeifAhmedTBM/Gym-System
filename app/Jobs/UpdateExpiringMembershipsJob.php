<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Status;
use App\Models\Reminder;
use App\Models\Membership;
use Illuminate\Bus\Queueable;
use App\Models\MemberReminder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpdateExpiringMembershipsJob implements ShouldQueue
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
     * Get all memberships which about to be expired and append expiring days to it
     *
     */
    public function getExpiringMemberships()
    {
        $memberships = Membership::where('status', 'active')->get();
        foreach($memberships as $membership) {
            if($membership->service_pricelist->service->service_type->session_type == 'sessions') {
                if(date('Y-m-d') == date('Y-m-d', strtotime($membership->end_date . '+' . $membership->service_pricelist->expiring_session . ' Days'))) {
                    Reminder::create([
                        'due_date'      => date('Y-m-d'),
                        'lead_id'       => $membership->member_id,
                        'user_id'       => $membership->sales_by_id != NULL ? $membership->sales_by_id : User::first()->id
                    ]);

                    $membership->member->update([
                        'status_id'     => Status::firstOrCreate(
                            ['name'     => 'Expiring Member'], 
                            ['color'    => 'warning', 'default_next_followup_days' => 2, 'need_followup' => 'yes']
                        )->id
                    ]);
                }
            }elseif($membership->service_pricelist->service->service_type->session_type == 'non-sessions') {
                if(date('Y-m-d') == date('Y-m-d', strtotime($membership->end_date . '+' . $membership->service_pricelist->expiring_date . ' Days'))) {
                    Reminder::create([
                        'due_date'      => date('Y-m-d'),
                        'lead_id'       => $membership->member_id,
                        'user_id'       => $membership->sales_by_id != NULL ? $membership->sales_by_id : User::first()->id
                    ]);
    
                    $membership->member->update([
                        'status_id'     => Status::firstOrCreate(
                            ['name'     => 'Expiring Member'], 
                            ['color'    => 'danger', 'default_next_followup_days' => 2, 'need_followup' => 'yes']
                        )->id
                    ]);
                }
            }
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
