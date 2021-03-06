<?php

namespace App\Http\Requests;

use App\Http\Requests;
use Route;
use Input;

class FilterRequest extends Request
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
        $id = Route::input('id')?Route::input('id'):'';
        return [
            'name' => 'required|max:64|unique:filter,name,'.$id,
        ];
    }
}
