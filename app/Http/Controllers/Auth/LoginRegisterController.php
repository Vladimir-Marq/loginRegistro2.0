<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\View;
use App\Rules\ReCaptcha;
use App\Http\Controllers\Auth\LoginTokenController; // Importa el LoginTokenController
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\LoginTokenMail;
use Illuminate\Support\Facades\DB;

class LoginRegisterController extends Controller
{
    /**
     * Esta clase se encarga de manejar las operaciones de inicio de sesión y registro de usuarios.
     */
    public function __construct() // Este constructor se encarga de especificar que rutas pueden ser accedidas por usuarios autenticados y no autenticados
    {
        $this->middleware('guest')->except(['logout', 'home']); // Aqui se especifican las rutas que pueden ser accedidas por usuarios no autenticados
        $this->middleware('auth')->only('logout', 'home'); // Aqui se especifican las rutas que pueden ser accedidas por usuarios autenticados
        $this->middleware('verified')->only('home'); // Aqui se especifican las rutas que pueden ser accedidas por usuarios autenticados y verificados
    }

    /**
     * Esta funcion despliega el formulario de registro.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Esta función se encarga de registrar un nuevo usuario en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request //
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        error_log('store method called'); // Imprime un mensaje cuando se llama al método

        try {
            $request->validate([
                'name' => 'required|string|max:250',
                'email' => 'required|string|email:rfc,dns|max:250|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'g-recaptcha-response' => ['required', new ReCaptcha]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            error_log('Validation errors: ' . print_r($e->errors(), true));
            throw $e;
        }

        error_log('validation passed'); // Imprime un mensaje después de que la validación pase

        // Checa si el usuario es el primero en registrarse, si es así, lo marca como administrador
        $isAdmin = (User::count() === 0);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $isAdmin, // Si no hay usuarios, el primero en registrarse será admin
        ]);

        error_log('user created'); // Imprime un mensaje después de que el usuario se cree

        event(new Registered($user)); // Evento de registro

        $credentials = $request->only('email', 'password'); // Credenciales del usuario
        Auth::attempt($credentials); // Autentica al usuario

        error_log('user authenticated'); // Imprime un mensaje después de que el usuario se autentique

        $request->session()->regenerate(); // Regenera la sesión
        return redirect()->route('verification.notice'); // Redirige al usuario a la vista de verificación de correo
    }

    /**
     * Esta funcion despliega el formulario de inicio de sesión.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Esta función se encarga de autenticar a un usuario en la aplicación.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        //dd($request->all()); // Volcar y morir los datos de la solicitud

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (Auth::attempt($credentials)) {
            // Genera un token
            $token = Str::random(60);
    
            // Guarda el token en la base de datos con una caducidad
            DB::table('login_tokens')->insert([
                'user_id' => Auth::user()->id,
                'token' => hash('sha256', $token),
                'expires_at' => now()->addMinutes(60),
            ]);
    
            // Envía el token al correo electrónico del usuario
            Mail::to($request->email)->send(new LoginTokenMail($token));
    
            return redirect()->route('token.verify');
        }
    
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son válidas.',
        ])->onlyInput('email');
    }

    public function verifyToken(Request $request)
    {
        //dd($request->token); // Volcar y morir el token proporcionado

        // Verifica el token
        $loginToken = DB::table('login_tokens')->where('token', hash('sha256', $request->token))->first();

        //dd($loginToken); // Volcar y morir el token encontrado en la base de datos

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
    
    /**
     * Esta función redirige al usuario a la vista de bienvenida correspondiente.
     *
     * @return \Illuminate\Http\Response // Redirige al usuario a la vista de bienvenida correspondiente
     */
    public function home()
    {
        $user = Auth::user(); // Obtiene el usuario autenticado

        if ($user->is_admin) {
            // Si el usuario es admin, redirige a la vista de bienvenida de admin
            return View::make('admin.welcome');
        } else {
            // Si el usuario es normal, redirige a la vista de bienvenida de usuario normal
            return View::make('user.welcome');
        }
    } 
    
    /**
     * Esta función se encarga de cerrar la sesión de un usuario en la aplicación.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) // Esta función se encarga de cerrar la sesión de un usuario en la aplicación
    {
        Auth::logout(); // Cierra la sesión del usuario
        $request->session()->invalidate(); // Invalida la sesión
        $request->session()->regenerateToken(); // Regenera el token de la sesión
        return redirect()->route('login') // Redirige al usuario al formulario de inicio de sesión con un mensaje de éxito
            ->withSuccess('Has cerrado sesión correctamente.'); // Mensaje de éxito
    }    

}