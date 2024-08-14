<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Models\Marketing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        $marketing = Marketing::all();

        return view('admin.marketing.settings.index', compact('marketing'));
    }


    public function saveWhatsappSettings(Request $request)
    {
        $settings = array();
        $settings['wassenger_token'] = $request['wassenger_token'];
        Marketing::updateOrCreate(
            ['service' => 'whatsapp'],
            ['settings' => json_encode($settings)]
        );
        $this->created();
        return back();
    }

    public function saveSmsSettings(Request $request)
    {
        $settings = array();
        $settings['username'] = $request['username'];
        $settings['password'] = $request['password'];
        $settings['account_sid'] = $request['account_sid'];
        // $settings['phone'] = $request['phone'];
        // $settings['auth_token'] = $request['auth_token'];
        Marketing::updateOrCreate(
            ['service' => 'sms'],
            ['settings' => json_encode($settings)]
        );
        $this->created();
        return back();
    }

    public function saveSmtpSettings(Request $request)
    {
        $settings = array();
        $settings['MAIL_HOST']          = $request['MAIL_HOST'];
        $settings['MAIL_PORT']          = $request['MAIL_PORT'];
        $settings['MAIL_USERNAME']      = $request['MAIL_USERNAME'];
        $settings['MAIL_PASSWORD']      = $request['MAIL_PASSWORD'];
        $settings['MAIL_ENCRYPTION']    = $request['MAIL_ENCRYPTION'];
        $settings['MAIL_FROM_ADDRESS']  = $request['MAIL_FROM_ADDRESS'];
        $settings['MAIL_FROM_NAME']     = $request['MAIL_FROM_NAME'];
        Marketing::updateOrCreate(
            ['service' => 'smtp'],
            ['settings' => json_encode($settings)]
        );
        $this->created();
        return back();
    }

    public function saveZoomSettings(Request $request)
    {
        $settings = array();
        $settings['ZOOM_ACCOUNT_ID']          = $request['ZOOM_ACCOUNT_ID'];
        $settings['ZOOM_CLIENT_ID']           = $request['ZOOM_CLIENT_ID'];
        $settings['ZOOM_CLIENT_SECRET']       = $request['ZOOM_CLIENT_SECRET'];
        $settings['ZOOM_CACHE_TOKEN']         = $request['ZOOM_CACHE_TOKEN'];

        Marketing::updateOrCreate(
            ['service' => 'zoom'],
            ['settings' => json_encode($settings)]
        );
        
        $this->created();
        return back();
    }
}
