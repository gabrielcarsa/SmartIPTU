<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DebitoRequest extends FormRequest
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
            'quantidade_parcela' => 'required|numeric',
            'tipo_debito_id' => 'required|numeric|min:1',
            'descricao_debito_id' => 'required|numeric|min:1',
            'valor_parcela' => 'required|numeric',
            'data_vencimento' => 'required|date',
            'valor_entrada' => 'nullable|numeric',
        ];
    }


    public function messages(): array
    {
        return [
            'min' => [
                'numeric' => 'O campo :attribute é obrigatório.',
            ],
        ];
    }
}
