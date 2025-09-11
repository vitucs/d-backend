<?php

declare(strict_types=1);

namespace App\Request\Transaction;

use Hyperf\Validation\Request\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payer_id' => 'required|integer|exists:users,id',
            'payee_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric|min:0.01'
        ];
    }

    public function messages(): array
    {
        return [
            'payer_id.required' => 'O ID do pagador é obrigatório.',
            'payer_id.integer'  => 'O ID do pagador deve ser um número inteiro.',
            'payer_id.exists'   => 'O pagador informado não existe em nosso sistema.',

            'payee_id.required' => 'O ID do beneficiário é obrigatório.',
            'payee_id.integer'  => 'O ID do beneficiário deve ser um número inteiro.',
            'payee_id.exists'   => 'O beneficiário informado não existe em nosso sistema.',

            'amount.required' => 'O valor da transferência é obrigatório.',
            'amount.numeric'  => 'O valor deve ser um número válido.',
            'amount.min'      => 'O valor da transferência deve ser de no mínimo R$ :min.',
        ];
    }

    public function attributes(): array
    {
        return [
            'payer_id' => 'ID do pagador',
            'payee_id' => 'ID do beneficiário',
            'amount' => 'valor da transferência',
        ];
    }
}
