<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'empDetails'    => 'required',
            'uCat'          => 'required',
            'uType'         => 'required',
            'uDesig'         => 'required'
        ];
    }

    public function messages(){
        return [
            'empDetails.unique' => "Employee already exist"
        ];
    }
}
