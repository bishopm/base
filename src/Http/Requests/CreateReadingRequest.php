<?php

namespace Bishopm\Connexion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReadingRequest extends FormRequest
{
    public function rules()
    {
        return [
            'readingdate' => 'required',
            'readings'=>'required'
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
