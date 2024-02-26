<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    /**
     * Esta clase se encarga de manejar las operaciones de verificación de correo electrónico.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Esta función despliega la notificación de verificación de correo electrónico.
     *
     * @return \Illuminate\Http\Response // Regresa la vista de verificación de correo electrónico
     */
    public function notice(Request $request)
    {
        return $request->user()->hasVerifiedEmail() 
            ? redirect()->route('home') : view('auth.verify-email');
    }

    /**
     * // Esta función verifica el correo electrónico del usuario.
     *
     * @param  \Illuminate\Http\EmailVerificationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill(); // Marca el correo electrónico del usuario como verificado
        return redirect()->route('home');
    }

    /**
     * Esta función reenvía el correo electrónico de verificación.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()
        ->withSuccess('Un nuevo enlace de verificación ha sido enviado a tu correo electrónico.');
    }
}