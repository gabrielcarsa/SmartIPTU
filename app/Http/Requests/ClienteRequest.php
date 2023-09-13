<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
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
            'nome' => 'required|min:3',
            'cpf_cnpj' => 'required|numeric',
            'rua_end' => 'required',
            'bairro_end' => 'required',
            'numero_end' => 'required|numeric',
            'cidade_end' => 'required',
            'estado_end' => 'required',
            'cep_end' => 'required|numeric',
            'email' => 'required|email',
            'data_nascimento' => 'nullable|date',
        ];
    }
}