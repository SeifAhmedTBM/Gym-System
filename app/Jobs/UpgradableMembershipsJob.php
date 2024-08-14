<?php

namespace App\Jobs;

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

class UpgradableMembershipsJob implements ShouldQueue
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
     * Get all memberships that belongs to pricelist which has ( upgrade from value > 0 ) .
     *
     */
    public function getAllMemberships()
    {
        $memberships = Membership::whereHas('service_pricelist', function($q) {
            $q = $q->whereNotNull('upgrade_from');
        })->get();
        foreach($memberships as $membership) {
            if(date('Y-m-d', strtotime($membership->start_date . '+' . $membership->service_pricelist->upgrade_from. ' Days')) == date('Y-m-d')) {

                Reminder::create([
                    'lead_id'     => $membership->member_id,
                    'user_id'     => $membership->sales_by_id,
                    'due_date'    => date('Y-m-d', strtotime($membership->start_date . '+' . $membership->service_pricelist->upgrade_from. ' Days'))
                ]);

                $membership->member->update([
                    'status_id' => Status::firstOrCreate(
                        ['name' => 'Upgradable'],
                        ['color' => 'info', 'default_next_followup_days' => 2, 'need_followup' => 'yes']
                    )->id
                ]);
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
        $this->getAllMemberships();
    }
}
