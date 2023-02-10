<?php

namespace App\Http\Requests\Api\Auth;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', Rule::unique('users'), 'regex:/^[a-zA-Z ]+$/u','max:64','name_reg'],
            'mobile' => ['required', 'string', 'max:10', 'min:10', Rule::unique('users')],
            'email' => ['string', 'email', 'max:64', Rule::unique('users')],
            'department_id'=> ['numeric', Rule::exists('departments', 'id')],
            'password' => 'required|string|min:6|confirmed'
        ];
    }
}
