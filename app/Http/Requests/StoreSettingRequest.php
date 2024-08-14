<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'bail|required|string|max:191',
            'email' => 'bail|required|email',
            'phone_numbers' => 'bail|required|string|max:191',
            'address' => 'bail|required',
            'landline' => 'bail|required|string|max:191',
            'invoice_prefix' => 'bail|required|string|max:191',
            'member_prefix' => 'bail|required|string|max:191',
            'freeze_duration' => 'bail|required|in:weeks,days',
            'has_lockers' => 'nullable|in:1,0',
            'left_section' => 'nullable',
            'right_section' => 'nullable',
            'footer' => 'nullable',
            'menu_logo' => 'nullable|image|mimes:png,jpg',
            'login_logo' => 'nullable|image|mimes:png,jpg',
            'payroll_day' => 'nullable|numeric|min:1|max:31'
        ];
    }
}
