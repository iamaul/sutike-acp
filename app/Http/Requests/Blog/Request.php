<?php

namespace App\Http\Requests\Blog;

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
            if (auth()->user()->canIndexBlogs() || auth()->user()->canCreateBlogs() || auth()->user()->canEditBlogs()) return true;
            return false;
        } else if ($this->isMethod('post')) {
            if (auth()->user()->canStoreBlogs()) return true;
            return false;
        } else if ($this->isMethod('put') || $this->isMethod('patch')) {
            if (auth()->user()->canUpdateBlogs()) return true;
            return false;
        } else if ($this->isMethod('delete')) {
            if (auth()->user()->canDestroyBlogs()) return true;
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
                // 'name' => "required|string|max:255|unique:blogs,name",
            ];
        } else if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                // 'name' => "required|string|max:255|unique:blogs,name",
            ];
        } else {
            return [
                //
            ];
        }
    }
}
