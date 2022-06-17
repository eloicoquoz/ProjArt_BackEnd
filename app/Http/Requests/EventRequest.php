<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
            'Titre' => 'required|string',
            'Debut' => 'required|date|after:yesterday',
            'Fin' => 'required|date|after:yesterday',
            'Lieu' => 'required|string',
            'user_Email' => 'required|email',
            'Description' => 'required|string'
        ];
    }
}
