<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyRefundRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason'    =>  'required',
        ];
    }

    public function attributes()
    {
        return [
            'reason'    =>  '原因',
        ];
    }
}
