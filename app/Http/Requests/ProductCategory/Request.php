<?php

namespace App\Http\Requests\ProductCategory;

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
            if (auth()->user()->canIndexProductCategories() || auth()->user()->canCreateProductCategories() || auth()->user()->canEditProductCategories()) return true;
            return false;
        } else if ($this->isMethod('post')) {
            if (auth()->user()->canStoreProductCategories()) return true;
            return false;
        } else if ($this->isMethod('put') || $this->isMethod('patch')) {
            if (auth()->user()->canUpdateProductCategories()) return true;
            return false;
        } else if ($this->isMethod('delete')) {
            if (auth()->user()->canDestroyProductCategories()) return true;
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
                // 'name' => "required|string|max:255|unique:product_categories,name",
            ];
        } else if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                // 'name' => "required|string|max:255|unique:product_categories,name",
            ];
        } else {
            return [
                //
            ];
        }
    }
}
