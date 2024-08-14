<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailCampaignRequest extends FormRequest
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
            'subject'   => 'bail|required|string|max:191',
            'message'   => 'bail|required',
            'emails'    => 'bail|required_if:emails_csv,null',
            'emails_csv' => 'bail|required_if:emails,null'
        ];
    }
}
