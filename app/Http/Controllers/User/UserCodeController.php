<?php

namespace App\Http\Controllers\User;

use App\Models\UserCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserCodeController extends Controller
{
    // Método para generar y guardar un código para un usuario
    public function generateCode(Request $request){
        $userId = $request->user_id;
        // Generar un código único (por ejemplo, de 6 dígitos o una cadena aleatoria)
        $code = Str::random(6);

        // Guardar el código en la tabla `user_codes`
        $userCode = UserCode::create([
            'user_id' => $userId,
            'code' => $code,
            'type' => 'verification', // o cualquier otro tipo necesario
            'expires_at' => Carbon::now()->addMinutes(10) // Ejemplo de expiración en 10 minutos
        ]);

        // Retornar respuesta o mensaje
        return response()->json(['message' => 'Código generado', 'code' => $code], 201);
    }

    // Método para verificar si el código es válido
    public function verifyCode(Request $request){
        $userId = $request->user_id;
        $code = $request->code;

        // Buscar el código en la tabla `user_codes`
        $userCode = UserCode::where('user_id', $userId)
                            ->where('code', $code)
                            ->where('expires_at', '>', Carbon::now()) // Asegurarse de que no haya expirado
                            ->first();

        if ($userCode) {
            return response()->json(['message' => 'Código válido'], 200);
        } else {
            return response()->json(['message' => 'Código inválido o expirado'], 400);
        }
    }

    public function showCodes() {
        $userCodes = UserCode::all();
        return "funciono"; 
    }
}