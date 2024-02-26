<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginTokenMail;
use App\Models\User;
use Auth;

class LoginTokenController extends Controller
{
    /**
     * Esta función genera un token de inicio de sesión y lo envía por correo electrónico.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Genera un token
            $token = Str::random(60);

            // Guarda el token en la base de datos con una caducidad
            DB::table('login_tokens')->insert([
                'user_id' => $user->id,
                'token' => hash('sha256', $token),
                'expires_at' => now()->addMinutes(60),
            ]);

            // Envía el token al correo electrónico del usuario
            Mail::to($request->email)->send(new LoginTokenMail($token));

            return back()->with('status', 'Por favor, verifica tu correo electrónico para iniciar sesión');
        }

        return back()->withErrors([
            'email' => 'El correo electrónico proporcionado no está registrado.',
        ])->onlyInput('email');
    }

    /**
     * Esta función verifica el token de inicio de sesión y autentica al usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        // Verifica el token
        $loginToken = DB::table('login_tokens')->where('token', hash('sha256', $request->token))->first();

        if ($loginToken && $loginToken->expires_at > now()) {
            // Si el token es válido, autentica al usuario
            Auth::loginUsingId($loginToken->user_id);

            // Elimina el token
            DB::table('login_tokens')->where('token', hash('sha256', $request->token))->delete();

            return redirect()->route('home');
        } else {
            // Si el token no es válido o ha caducado, muestra un mensaje de error
            return back()->with('status', 'El token es inválido o ha caducado');
        }
    }
}