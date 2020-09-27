<?php

namespace App\Http\Requests\BlogTag;

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
            if (auth()->user()->canIndexBlogTags() || auth()->user()->canCreateBlogTags() || auth()->user()->canEditBlogTags()) return true;
            return false;
        } else if ($this->isMethod('post')) {
            if (auth()->user()->canStoreBlogTags()) return true;
            return false;
        } else if ($this->isMethod('put') || $this->isMethod('patch')) {
            if (auth()->user()->canUpdateBlogTags()) return true;
            return false;
        } else if ($this->isMethod('delete')) {
            if (auth()->user()->canDestroyBlogTags()) return true;
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
                // 'name' => "required|string|max:255|unique:blog_tags,name",
            ];
        } else if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                // 'name' => "required|string|max:255|unique:blog_tags,name",
            ];
        } else {
            return [
                //
            ];
        }
    }
}
