<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidarCalendarios implements Rule
{
    public function passes($attribute, $value)
    {
        // Obtener los valores de los objetos Calendar desde la solicitud
        $calendar1 = request()->input('advance_validity_from');
        $calendar2 = request()->input('advance_validity_to');

        // Verificar si uno de los calendarios está vacío y el otro tiene datos
        return (empty($calendar1) && !empty($calendar2)) || (!empty($calendar1) && empty($calendar2));
    }

    public function message()
    {
        return 'Uno de los calendarios debe tener datos mientras que el otro está vacío.';
    }
}
