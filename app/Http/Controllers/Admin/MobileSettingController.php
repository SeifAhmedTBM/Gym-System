<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobileSetting;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MobileSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = MobileSetting::first();

        return view('admin.mobile_settings.settings', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'privacy_setting' => '',
            'about_us' => '',
            'rules' => '',
            'terms_conditions' => '',
            'pt_service_type' => '',
            'membership_service_type' => '',
            'phone_number' => '',
            'whatsapp_number' => '',
            'facebook_url' => '',
            'instagram_url' => '',
            'tiktok_url' => '',
            'payment_account_id'=> ''

        ]);
        $settings = new MobileSetting();
        $settings->privacy_setting                     = $request['privacy_setting'] ?? '';
        $settings->about_us                     = $request['about_us'] ?? '';
        $settings->rules                     = $request['rules'] ?? '';
        $settings->terms_conditions                     = $request['terms_conditions'] ?? '';
        $settings->pt_service_type                     = $request['pt_service_type'] ?? 2;
        $settings->classes_service_type                     = $request['classes_service_type'] ?? null;
        $settings->membership_service_type                     = $request['membership_service_type'] ?? null;
        $settings->phone_number                     = $request['phone_number'] ?? null;
        $settings->whatsapp_number                     = $request['whatsapp_number'] ?? null;
        $settings->facebook_url                     = $request['facebook_url'] ?? null;
        $settings->instagram_url                     = $request['instagram_url'] ?? null;
        $settings->tiktok_url                     = $request['tiktok_url'] ?? null;
        $settings->account_id                     = $request['payment_account_id'] ?? null;

        $settings->save();
        Alert::success('Saved Successfully');
        return redirect()->route('admin.mobile_settings.index');
    }

    /**
     * Display the specified resource.
     */



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MobileSetting $mobileSetting)
    {
        //

        $mobileSetting->privacy_setting                     = $request['privacy_setting'] ?? '';
        $mobileSetting->about_us                     = $request['about_us'] ?? '';
        $mobileSetting->rules                     = $request['rules'] ?? '';
        $mobileSetting->terms_conditions                     = $request['terms_conditions'] ?? '';
        $mobileSetting->classes_service_type                     = $request['classes_service_type'] ?? null;
        $mobileSetting->pt_service_type                     = $request['pt_service_type'] ?? null;
        $mobileSetting->membership_service_type                     = $request['membership_service_type'] ?? null;
        $mobileSetting->phone_number                     = $request['phone_number'] ?? null;
        $mobileSetting->whatsapp_number                     = $request['whatsapp_number'] ?? null;
        $mobileSetting->facebook_url                     = $request['facebook_url'] ?? null;
        $mobileSetting->instagram_url                     = $request['instagram_url'] ?? null;
        $mobileSetting->tiktok_url                     = $request['tiktok_url'] ?? null;
        $mobileSetting->account_id                     = $request['payment_account_id'] ?? null;
        $mobileSetting->save();
        Alert::success('Saved Successfully');
        return redirect()->route('admin.mobile_settings.index');
    }

}
