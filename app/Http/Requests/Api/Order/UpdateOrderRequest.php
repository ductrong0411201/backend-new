<?php

namespace App\Http\Requests\Api\Order;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
            'name' => ['string','max:254', 'min:1'],
            'project_name' => ['string', 'max:254', 'min:1'],
            'structure_id' => ['numeric', Rule::exists('structures', 'id')],
            'department_id'=> ['numeric', Rule::exists('departments', 'id')],
            'funding_agency_id'=> ['numeric', Rule::exists('funding_agencies', 'id')],
        ];
    }
}
