<?php

declare(strict_types=1);

namespace App\Request\User;

use App\Request\Rules\StrongPassword;
use App\Request\Rules\ValidCpf;
use Hyperf\Validation\Request\FormRequest;

class RegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255|min:2',
            'document'  => ['required', 'string', 'unique:users,document', new ValidCpf()],
            'email'     => ['required', 'email:rfc,dns', 'unique:users,email'],
            'password'  => ['required', 'string', new StrongPassword()],
            'password_confirmation' => 'required|same:password',
            'type'      => 'required|string|in:common,shopkeeper'
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'O nome completo é obrigatório.',
            'full_name.min' => 'O nome deve ter pelo menos 2 caracteres.',
            'full_name.max' => 'O nome não pode ter mais de 255 caracteres.',
            
            'document.required' => 'O CPF é obrigatório.',
            'document.unique' => 'Este CPF já está cadastrado.',
            
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            
            'password.required' => 'A senha é obrigatória.',
            
            'password_confirmation.required' => 'Confirme sua senha.',
            'password_confirmation.same' => 'As senhas não coincidem.',
            
            'type.required' => 'Selecione o tipo de usuário.',
            'type.in' => 'Tipo deve ser "common" ou "shopkeeper".',
        ];
    }

    public function attributes(): array
    {
        return [
            'full_name' => 'nome completo',
            'document' => 'CPF',
            'email' => 'e-mail',
            'password' => 'senha',
            'password_confirmation' => 'confirmação da senha',
            'type' => 'tipo de usuário'
        ];
    }
}
