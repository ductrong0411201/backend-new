<?php

namespace App\Http\Requests\Api\Project;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->user()->hasRoles([1, 2]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => ['required','integer', Rule::unique('projects', 'order_id')],
            'uuid' => ['required','string','max:500', 'min:6', Rule::unique('projects', 'uuid')],
        ];
    }
}
