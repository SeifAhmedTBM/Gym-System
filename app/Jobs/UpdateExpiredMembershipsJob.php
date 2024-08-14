<?php

namespace App\Jobs;

use App\Models\Membership;
use App\Models\Status;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateExpiredMembershipsJob implements ShouldQueue
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

    public function getExpiredMemberships()
    {
        $memberships = Membership::where('end_date', date('Y-m-d'))->where('status', 'active')->get();
        foreach($memberships as $membership) {
            $membership->update(['status' => 'inactive']);
            $membership->member->update([
                'status_id'     => Status::firstOrCreate(
                    ['name'     => 'Expired Member'], 
                    ['color'    => 'danger', 'default_next_followup_days' => 2, 'need_followup' => 'yes'])->id
            ]);
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
