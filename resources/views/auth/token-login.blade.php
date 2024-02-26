<!-- resources/views/emails/login.blade.php -->

<h1>Bienvenido a nuestra aplicación</h1>

<p>
    Haz clic en el siguiente enlace para iniciar sesión:
    <a href="{{ url('/login/verify?token=' . $token) }}">
        Iniciar sesión
    </a>
</p>