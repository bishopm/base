<?php

namespace bishopm\base\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateResourceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required'
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
        ];
    }
}