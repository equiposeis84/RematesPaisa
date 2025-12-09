<?php
// app/Rules/StrongPassword.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    public function passes($attribute, $value)
    {
        // Mínimo 6 caracteres, al menos una letra y un número
        return preg_match('/^(?=.*[A-Za-z])(?=.*\d).{6,}$/', $value);
    }
    
    public function message()
    {
        return 'La contraseña debe tener al menos 6 caracteres, incluyendo letras y números.';
    }
}