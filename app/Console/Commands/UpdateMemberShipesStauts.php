<?php

namespace App\Console\Commands;

use App\Models\Membership;
use Illuminate\Console\Command;

class UpdateMemberShipesStauts extends Command
{
    protected $signature = 'app:update-member-shipes-stauts';
    protected $description = 'Command description';

    public function handle()
    {
        $today = now()->format('Y-m-d');
        Membership::whereDate('start_date', $today)->update(['status' => 'current']);
        Membership::whereDate('end_date', $today)->update(['status' => 'expired']);
    }

}
