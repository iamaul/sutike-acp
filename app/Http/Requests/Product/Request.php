<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->isMethod('get')) {
            if (auth()->user()->canIndexProducts() || auth()->user()->canCreateProducts() || auth()->user()->canEditProducts()) return true;
            return false;
        } else if ($this->isMethod('post')) {
            if (auth()->user()->canStoreProducts()) return true;
            return false;
        } else if ($this->isMethod('put') || $this->isMethod('patch')) {
            if (auth()->user()->canUpdateProducts()) return true;
            return false;
        } else if ($this->isMethod('delete')) {
            if (auth()->user()->canDestroyProducts()) return true;
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
        if ($this->isMethod('post')) {
            return [
                // 'name' => "required|string|max:255|unique:products,name",
            ];
        } else if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                // 'name' => "required|string|max:255|unique:products,name",
            ];
        } else {
            return [
                //
            ];
        }
    }
}
