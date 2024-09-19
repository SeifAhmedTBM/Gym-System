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

        ]);
        $settings = new MobileSetting();
        $settings->privacy_setting                     = $request['privacy_setting'] ?? '';
        $settings->about_us                     = $request['about_us'] ?? '';
        $settings->rules                     = $request['rules'] ?? '';
        $settings->terms_conditions                     = $request['terms_conditions'] ?? '';
        $settings->pt_service_type                     = $request['pt_service_type'] ?? 2;
        $settings->classes_service_type                     = $request['classes_service_type'] ?? null;

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
        $mobileSetting->classes_service_type                     = $request['classes_service_type'] ?? 2;
        $mobileSetting->pt_service_type                     = $request['pt_service_type'] ?? 2;
        $mobileSetting->save();
        Alert::success('Saved Successfully');
        return redirect()->route('admin.mobile_settings.index');
    }

}
