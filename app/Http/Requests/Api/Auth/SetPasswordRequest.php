<?php

namespace App\Http\Requests\Api\Auth;

use Dingo\Api\Http\FormRequest;

/**
 * Class ChangePasswordRequest.
 */
class SetPasswordRequest extends FormRequest
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
            'password'   => 'required|min:4|confirmed'
        ];
    }
}
