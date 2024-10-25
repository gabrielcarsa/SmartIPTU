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
            'nome' => 'nullable|min:3',
            'razao_social' => 'nullable|min:3',
            'cpf' => 'nullable|numeric',
            'cnpj' => 'nullable|numeric',
            'inscricao_estadual' => 'nullable|numeric',
            'rua_end' => 'required',
            'bairro_end' => 'required',
            'numero_end' => 'required|numeric',
            'cidade_end' => 'required',
            'estado_end' => 'required',
            'cep_end' => 'required|string',
            'email' => 'nullable|email',
            'data_nascimento' => 'nullable|date',
        ];
    }
}