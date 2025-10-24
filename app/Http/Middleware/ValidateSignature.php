<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ValidateSignature as BaseValidateSignature;

class ValidateSignature extends BaseValidateSignature
{
    // Este middleware hereda toda la funcionalidad del base
    // No necesitas agregar nada más
}