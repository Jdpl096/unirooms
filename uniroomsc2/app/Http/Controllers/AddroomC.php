<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Actions\AddRoom;
use App\Actions\UploadRoomImage;
use Illuminate\Validation\ValidationException;

class AddroomC extends Controller
{
    public function store(Request $request, AddRoom $addRoom, UploadRoomImage $uploadRoomImage)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'precio' => 'required|numeric|min:0',
                'capacidad' => 'required|integer|min:1',
                'ubicacion_especifica' => 'required|string|max:255',
                'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            // Subir imágenes y obtener URLs
            $urls = [];
            if ($request->hasFile('imagenes')) {
                $urls = $uploadRoomImage->handle($request->file('imagenes')); // debe devolver array de URLs
            }

            // Crear habitación
            $pension = $addRoom->handle(Auth::user(), [
                ...$request->all(),
                'imagenes' => $urls, // aquí enviamos solo el array de URLs
            ]);

            return back()->with('success', 'Habitación registrada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();

        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al registrar la habitación: ' . $e->getMessage());
        }
    }
}
