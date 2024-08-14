<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSettingRequest;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();

        return view('admin.settings.settings', compact('settings'));
    }

    public function store(StoreSettingRequest $request)
    {
        $settings = new Setting();
        $settings->name                     = $request['name'];
        $settings->phone_numbers            = $request['phone_numbers'];
        $settings->email                    = $request['email'];
        $settings->landline                 = $request['landline'];
        $settings->address                  = $request['address'];
        $settings->invoice_prefix           = $request['invoice_prefix'];
        $settings->member_prefix            = $request['member_prefix'];
        $settings->freeze_duration          = $request['freeze_duration'];
        $settings->freeze_request           = $request['freeze_request'] == 1 ? true : false;
        $settings->has_lockers              = $request['has_lockers'] == 1 ? true : false;
        $settings->color                    = $request['color'];
        $settings->max_discount             = $request['max_discount'];
        $settings->terms                    = $request['terms'];
        $settings->privacy                  = $request['privacy'];
        $settings->facebook                 = $request['facebook'];
        $settings->instagram                = $request['instagram'];
        $settings->gmail                    = $request['gmail'];
        $settings->whatsapp                 = $request['whatsapp'];
        $settings->long                     = $request['long'];
        $settings->lat                      = $request['lat'];
        $settings->inactive_members_days    = $request['inactive_members_days'];

        if($file = $request->file('menu_logo')) {
            $name = $file->getClientOriginalName();
            $file->move('images', $name);
            $settings->menu_logo = $name;
        }
        if($file = $request->file('login_logo')) {
            $name = $file->getClientOriginalName();
            $file->move('images', $name);
            $settings->login_logo = $name;
        }

        if($file = $request->file('login_background')) {
            $name = $file->getClientOriginalName();
            $file->move('images', $name);
            $settings->login_background = $name;
        }

        $invoice_sections = [];
        $invoice_sections['left_section'] = $request['left_section'];
        $invoice_sections['right_section'] = $request['right_section'];
        $invoice_sections['footer'] = $request['invoice_footer'];
        $settings->payroll_day = $request['payroll_day'];
        $settings->invoice = json_encode($invoice_sections, true);
        $settings->save();
        Alert::success('Saved Successfully');
        return redirect()->route('admin.settings.index');
    }

    public function update(StoreSettingRequest $request, $id)
    {
        $settings                           = Setting::findOrFail($id);
        $settings->name                     = $request['name'];
        $settings->phone_numbers            = $request['phone_numbers'];
        $settings->email                    = $request['email'];
        $settings->landline                 = $request['landline'];
        $settings->address                  = $request['address'];
        $settings->invoice_prefix           = $request['invoice_prefix'];
        $settings->member_prefix            = $request['member_prefix'];
        $settings->freeze_duration          = $request['freeze_duration'];
        $settings->freeze_request           = $request['freeze_request'] == 1 ? true : false;
        $settings->has_lockers              = $request['has_lockers']  == 1 ? true : false;
        $settings->color                    = $request['color'];
        $settings->max_discount             = $request['max_discount'];
        $settings->payroll_day              = $request['payroll_day'];
        $settings->terms                    = $request['terms'];
        $settings->privacy                  = $request['privacy'];
        $settings->facebook                 = $request['facebook'];
        $settings->instagram                = $request['instagram'];
        $settings->gmail                    = $request['gmail'];
        $settings->whatsapp                 = $request['whatsapp'];
        $settings->long                     = $request['long'];
        $settings->lat                      = $request['lat'];
        $settings->inactive_members_days    = $request['inactive_members_days'];
        
        if($file = $request->file('menu_logo')) {
            $name = $file->getClientOriginalName();
            $file->move('images', $name);
            $settings->menu_logo = $name;
        }
        if($file = $request->file('login_logo')) {
            $name = $file->getClientOriginalName();
            $file->move('images', $name);
            $settings->login_logo = $name;
        }

        if($file = $request->file('login_background')) {
            $name = $file->getClientOriginalName();
            $file->move('images', $name);
            $settings->login_background = $name;
        }

        $invoice_sections = [];
        $invoice_sections['left_section'] = $request['left_section'];
        $invoice_sections['right_section'] = $request['right_section'];
        $invoice_sections['footer'] = $request['invoice_footer'];
        $settings->invoice = json_encode($invoice_sections, true);
        $settings->save();
        Alert::success('Saved Successfully');
        return redirect()->route('admin.settings.index');
    }

    public function masterData() : View
    {
        return view('admin.master_data.index');
    }
}
