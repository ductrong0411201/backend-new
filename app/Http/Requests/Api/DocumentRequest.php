<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

/**
 * Class DocumentRequest.
 */
class DocumentRequest extends FormRequest
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
            'file' => 'required|file|max:4096|mimes:pdf,docx,doc,text/csv,xls,xlsx,zip,rar'
        ];
    }
}
