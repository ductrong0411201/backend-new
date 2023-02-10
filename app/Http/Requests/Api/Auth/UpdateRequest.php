<?php

namespace App\Http\Requests\Api\Auth;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'name' => ['string','max:64', Rule::unique('users')->ignore($this->id)],
            'mobile' => ['string','max:11', 'min:10'],
            'email' => ['string', 'email', 'max:64'],
            'district' => ['string','max:64', 'min:1', Rule::exists('areas', 'dist_name')],
            'department_id'=> ['numeric', Rule::exists('departments', 'id')],
        ];
    }
}
