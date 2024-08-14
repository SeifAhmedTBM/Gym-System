<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;

class ZoomService
{
    public function branch_only($request,$zoom)
    {
        $users = User::whereHas('employee',fn($q) => $q->whereIn('branch_id',$request['branch_id']))->get();

        foreach ($users as $key => $user) 
        {
            Task::create([
                'title'                     => 'Zoom Meeting | '.$request['topic'],
                'description'               => Auth()->user()->name .' make zoom meeting must join at '.$request['date'].' '.$request['start_time'].' .. Link : '.'<a href="'.$zoom['data']['join_url'].'" class="text-info">'.$zoom['data']['join_url'].'</a>',
                'to_user_id'                => $user->user_id,
                'to_role_id'                => NULL,
                'created_by_id'             => Auth()->id(),
                'task_date'                 => $request['date'],
            ]);
        }
    }

    public function roles_only($request,$zoom)
    {
        $users = User::whereHas('roles',fn($q) => $q->whereIn('id',$request['role_id']))
                            ->whereHas('employee',fn($q) => $q->whereStatus('active'))
                            ->get();

        foreach ($users as $key => $user) 
        {
            Task::create([
                'title'                     => 'Zoom Meeting | '.$request['topic'],
                'description'               => Auth()->user()->name .' make zoom meeting must join at '.$request['date'].' '.$request['start_time'].' .. Link : '.'<a href="'.$zoom['data']['join_url'].'" class="text-info">'.$zoom['data']['join_url'].'</a>',
                'to_user_id'                => $user->id,
                'to_role_id'                => NULL,
                'created_by_id'             => Auth()->id(),
                'task_date'                 => $request['date'],
            ]);
        }
    }

    public function branch_roles($request,$zoom)
    {
        $users = User::whereHas('employee',fn($q) => $q->whereIn('branch_id',$request['branch_id'])->whereStatus('active'))
                            ->whereHas('roles',fn($q) => $q->whereIn('id',$request['role_id']))
                            ->get();

        foreach ($users as $key => $user) 
        {
            Task::create([
                'title'                     => 'Zoom Meeting | '.$request['topic'],
                'description'               => Auth()->user()->name .' make zoom meeting must join at '.$request['date'].' '.$request['start_time'].' .. Link : '.'<a href="'.$zoom['data']['join_url'].'" class="text-info">'.$zoom['data']['join_url'].'</a>',
                'to_user_id'                => $user->id,
                'to_role_id'                => NULL,
                'created_by_id'             => Auth()->id(),
                'task_date'                 => $request['date'],
            ]);
        }
    }

    public function none($request,$zoom)
    {
        $users = User::whereHas('employee',fn($q) => $q->whereStatus('active'))->get();
        
        foreach ($users as $key => $user) 
        {
            Task::create([
                'title'                     => 'Zoom Meeting | '.$request['topic'],
                'description'               => Auth()->user()->name .' make zoom meeting must join at '.$request['date'].' '.$request['start_time'].' .. Link : '.'<a href="'.$zoom['data']['join_url'].'" class="text-info">'.$zoom['data']['join_url'].'</a>',
                'to_user_id'                => $user->id,
                'to_role_id'                => NULL,
                'created_by_id'             => Auth()->id(),
                'task_date'                 => $request['date'],
            ]);
        }
    }
}