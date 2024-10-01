<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;

use App\Models\MobileSetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InformationApiController extends Controller
{
    private $setting;
    public function __construct(){
        $this->setting = MobileSetting::all()->first();
    }

    public function privacy() {
        return [
            'message'=>'success',
            'data'=>[
                'privacy'=>$this->setting->privacy_setting
            ]
        ];
    }
    public function about_us() {
        return [
            'message'=>'success',
            'data'=>[
                'about_us'=>$this->setting->about_us
            ]
        ];
    }
    public function contact_us() {
        return [
            'message'=>'success',
            'data'=>[
                'phone_number'=>$this->setting->phone_number,
                'whatsapp_number'=>$this->setting->whatsapp_number,
                'facebook_url'=>$this->setting->facebook_url,
                'instagram_url'=>$this->setting->instagram_url,
                'tiktok_url'=>$this->setting->tiktok_url,
            ]
        ];
    }
    public function rules() {
        return [
            'message'=>'success',
            'data'=>[
                'rules'=>$this->setting->rules
            ]
        ];
    }
    public function terms_conditions() {
        return [
            'message'=>'success',
            'data'=>[
                'terms_conditions'=>$this->setting->terms_conditions
            ]
        ];
    }
}
