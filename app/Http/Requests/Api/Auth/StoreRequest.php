<?php

namespace App\Http\Requests\Api\Auth;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->user()->hasRoles([1]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:64', Rule::unique('users')],
            'mobile' => ['required', 'string', 'max:11', 'min:10', Rule::unique('users')],
            'email' => ['string', 'email', 'max:64', Rule::unique('users')],
            'password' => 'required|string|min:6|confirmed',
            'district' => ['string', 'max:64', 'min:1', Rule::exists('areas', 'dist_name')],
            'department_id' => ['numeric', Rule::exists('departments', 'id')],
        ];
    }
}