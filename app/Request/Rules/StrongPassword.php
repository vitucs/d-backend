<?php

declare(strict_types=1);

namespace App\Request\Rules;

use Hyperf\Validation\Contract\Rule;

class StrongPassword implements Rule
{
    private bool $lengthPasses = true;
    private bool $uppercasePasses = true;
    private bool $lowercasePasses = true;
    private bool $numericPasses = true;
    private bool $specialCharPasses = true;

    public function passes($attribute, $value): bool
    {
        $this->lengthPasses = strlen($value) >= 8;
        $this->uppercasePasses = (bool) preg_match('/[A-Z]/', $value);
        $this->lowercasePasses = (bool) preg_match('/[a-z]/', $value);
        $this->numericPasses = (bool) preg_match('/[0-9]/', $value);
        $this->specialCharPasses = (bool) preg_match('/[^A-Za-z0-9]/', $value);

        return $this->lengthPasses && 
               $this->uppercasePasses && 
               $this->lowercasePasses && 
               $this->numericPasses && 
               $this->specialCharPasses;
    }

    public function message(): string
    {
        $errors = [];
        
        if (!$this->lengthPasses) {
            $errors[] = 'pelo menos 8 caracteres';
        }
        if (!$this->uppercasePasses) {
            $errors[] = 'pelo menos 1 letra maiúscula';
        }
        if (!$this->lowercasePasses) {
            $errors[] = 'pelo menos 1 letra minúscula';
        }
        if (!$this->numericPasses) {
            $errors[] = 'pelo menos 1 número';
        }
        if (!$this->specialCharPasses) {
            $errors[] = 'pelo menos 1 caractere especial';
        }

        return 'A senha deve ter ' . implode(', ', $errors) . '.';
    }
}
