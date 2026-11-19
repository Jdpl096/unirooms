<?php

namespace App\Actions;

use App\Models\Pension;
use Illuminate\Validation\ValidationException;

class AddRoom
{
    public function handle($user, array $input)
    {
        // Extraer y limpiar los datos del formulario
        $nombre = trim($input['nombre'] ?? '');
        $precio = trim($input['precio'] ?? '');
        $capacidad = trim($input['capacidad'] ?? '');
        $ubicacion_especifica = trim($input['ubicacion_especifica'] ?? '');
        $descripcion = trim($input['descripcion'] ?? '');
        $id_tipo_habitacion = trim($input['id_tipo_habitacion'] ?? '');
        $id_zona = trim($input['id_zona'] ?? '');
        $id_estado = trim($input['id_estado'] ?? '') ?: 2; // el formulario no envía estado: 2 = 'publicada' (1 = 'borrador')
        $id_servicio = $input['amenities'] ?? [];

        // 🔍 Validaciones básicas
        if (empty($nombre)) {
            throw ValidationException::withMessages(['nombre' => 'El nombre es obligatorio']);
        }

        if (empty($capacidad) || !is_numeric($capacidad) || $capacidad <= 0) {
            throw ValidationException::withMessages(['capacidad' => 'Debe ser un número entero positivo']);
        }

        if (empty($precio) || !is_numeric($precio) || $precio < 0) {
            throw ValidationException::withMessages(['precio' => 'Debe ser un número positivo']);
        }

        if (empty($ubicacion_especifica)) {
            throw ValidationException::withMessages(['ubicacion_especifica' => 'La ubicación es obligatoria']);
        }

        // 🏠 Crear la pensión y guardar links sin escapar las barras (/)
        $pension = Pension::create([
            'id_propietario' => $user->persona->propietario->id ?? null,
            'nombre' => $nombre,
            'precio' => $precio,
            'capacidad' => $capacidad,
            'ubicacion_especifica' => $ubicacion_especifica,
            'disponible' => $input['disponible'] ?? true,
            'link_foto' => !empty($input['imagenes'])
                ? json_encode($input['imagenes'], JSON_UNESCAPED_SLASHES)
                : '[]', // link_foto es NOT NULL
            'id_tipo_habitacion' => $id_tipo_habitacion,
            'id_zona' => $id_zona,
            'id_estado' => $id_estado,
            'descripcion' => $descripcion,
        ]);

        // 🧩 Asociar servicios si existen
        if (!empty($id_servicio)) {
            $pension->servicios()->attach($id_servicio);
        }

        return $pension;
    }
}
