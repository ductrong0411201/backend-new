<?php

namespace App\Http\Requests\Api\Order;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->user()->hasRoles([1, 2, 4, 5]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', 'max:191', 'min:1'],
            'construction_name' => ['string', 'max:500', 'min:1'],
            'circle' => ['string', 'max:64', 'min:1'],
            'structure_id' => ['numeric', Rule::exists('structures', 'id')],
            'district' => ['string', 'max:64', 'min:1'],
            'department_id' => ['numeric', Rule::exists('departments', 'id')],
            'funding_agency_id' => ['numeric', Rule::exists('funding_agencies', 'id')],
        ];
    }
}
