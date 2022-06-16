<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemarqueRequest extends FormRequest
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
            'user_Email' => 'required|email',
            'cours_id' => 'required|integer',
            'Titre' => 'required|string',
            'Description' => 'required|string',
            'Visibilite' => 'required|string|in:public,prive',
            'Date' => 'required|date'
        ];
    }
}
