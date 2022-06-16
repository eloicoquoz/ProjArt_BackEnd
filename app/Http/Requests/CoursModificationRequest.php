<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoursModificationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'Debut' => 'required|date|after:yesterday',
            'Fin' => 'required|date|after:Debut',
            //Contains groups of 1 upper case letter with 3 numbers and possibly a lowercase letter at the end, each group is separated by a space
            'Salles' => 'required|regex:/^[A-Z]{1}[0-9]{3}[a-z]{0,1}([ ]{1}[A-Z]{1}[0-9]{3}[a-z]{0,1})*$/',
            'User' => 'required|email'
        ];
    }
}
