<?php

declare(strict_types=1);

namespace App\Request\Rules;

use Hyperf\Validation\Contract\Rule;

class ValidCpf implements Rule
{
    public function passes($attribute, $value): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $value);
        
        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return 'O :attribute deve ser um CPF válido.';
    }
}
