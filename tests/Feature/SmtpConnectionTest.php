<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SmtpConnectionTest extends TestCase
{
    /**
     * Test de conexión SMTP.
     *
     * @return void
     */
    public function testSmtpConnection()
    {
        try {
            // Intenta enviar un correo de prueba
            Mail::raw('Prueba de conexión SMTP desde mi proyecto de laravel', function ($message) {
                $message->to('kevinblaster117@gmail.com');
            });

            // Si llega hasta aquí sin lanzar excepciones, el test pasa
            $this->assertTrue(true);
        } catch (\Exception $e) {
            // Si hay una excepción, el test falla
            $this->fail('Error en la conexión SMTP: ' . $e->getMessage());
        }
    }
}