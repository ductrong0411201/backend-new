<?php

namespace App\Http\Requests\Api\Meeting;

use Dingo\Api\Http\FormRequest;

class StoreMeetingRequest extends FormRequest
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
            'title' => ['required','string','max:191', 'min:6'],
            'departments' => ['required'],
            'description' => ['string','max:1000'],
            'start_date'=> ['string'],
            'note'=> ['string']
        ];
    }
}
