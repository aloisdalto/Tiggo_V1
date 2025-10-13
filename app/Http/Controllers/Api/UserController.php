<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Mostrar perfil del usuario autenticado
    public function profile(Request $request)
    {
        $user = $request->user()->load('technicianProfile.services');

        return response()->json($user);
    }

    // Actualizar perfil
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'description' => 'sometimes|string', // para técnico
        ]);

        $user->update($request->only('name', 'phone', 'latitude', 'longitude'));

        if ($user->hasRole('tecnico')) {
            $profile = $user->technicianProfile;
            if ($profile) {
                $profile->update($request->only('description'));
            }
        }

        return response()->json(['message' => 'Perfil actualizado', 'user' => $user->fresh()->load('technicianProfile.services')]);
    }
}
