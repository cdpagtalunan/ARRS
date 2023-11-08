<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CutoffRequest extends FormRequest
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
            'froms'      => 'required',
            'to'        => 'required',
            'cutoff'       => 'required',
            'dateEmail'     => 'required'
        ];
    }

    // public function messages(){
    //     return [
    //         'cutoff.unique' => 'Cut off already exist'
    //     ];
    // }
}
