<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ControlPanelController extends Controller
{
    public function operations()
    {
        return view('partials.operations');
    }


    public function hot_keys()
    {
        return view('partials.hot_keys');
    }


    public function master_data()
    {
        return view('partials.master');
    }

    public function hr()
    {
        return view('partials.hr');
    }

    public function mobile()
    {
        return view('partials.mobile');
    }

    public function taskManagement()
    {
        return view('partials.task_management');
    }
}
