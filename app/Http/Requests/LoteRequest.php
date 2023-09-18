<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'lote' => 'required',
            'quadra_id' => 'required|numeric|min:1',
            'cliente_id' => 'required|numeric|min:1',
            'matricula' => 'required|numeric',
            'inscricao_municipal' => 'required|numeric',

        ];
    }
}
