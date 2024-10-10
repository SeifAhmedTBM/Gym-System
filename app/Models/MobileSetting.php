<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'privacy_setting',
        'about_us',
        'rules',
        'account_id',
        'terms_conditions',
        'pt_service_type',
        'classes_service_type',
        'membership_service_type',
        'phone_number',
        'whatsapp_number',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
    ];
}
