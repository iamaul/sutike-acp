<?php

namespace App\Http\Requests\ResetPass;

use Illuminate\Foundation\Http\FormRequest;

class ResetPass extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        
        if ($this->isMethod('resetPass')) {
            if (auth()->user()->canUpdateUsers()) return true;
            return false;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->isMethod('resetPass')) {
            return [
                'password' => 'string|max:50'
            ];
        } else {
            return [];
        }
    }
}
