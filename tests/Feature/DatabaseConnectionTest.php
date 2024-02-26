<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseConnectionTest extends TestCase
{
    /**
     * Test de conexión a la base de datos.
     *
     * @return void
     */
    public function testDatabaseConnection()
    {
        try {
            // Intenta ejecutar una consulta simple
            $connection = DB::connection()->getPdo();

            // Si llega hasta aquí sin lanzar excepciones, el test pasa
            $this->assertTrue(true);
        } catch (\Exception $e) {
            // Si hay una excepción, el test falla
            $this->fail('Error en la conexión a la base de datos: ' . $e->getMessage());
        }
    }
}
