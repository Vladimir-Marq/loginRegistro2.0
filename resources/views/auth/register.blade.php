@extends('auth.layouts')

@section('content')

<div class="row justify-content-center mt-5">
    <div class="col-md-8">

        <div class="card">
            <div class="card-header">Registro</div>
            <div class="card-body">
                <form action="{{ route('store') }}" method="post" id="store">
                    @csrf
                    <div class="mb-3 row">
                        <label for="name" class="col-md-4 col-form-label text-md-end text-start">Nombre de usuario</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
                            @if ($errors->has('name'))
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="email" class="col-md-4 col-form-label text-md-end text-start">Correo electronico</label>
                        <div class="col-md-6">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="password" class="col-md-4 col-form-label text-md-end text-start">Contraseña</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            @if ($errors->has('password'))
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="password_confirmation" class="col-md-4 col-form-label text-md-end text-start">Confirme contraseña</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <input type="submit" class="col-md-3 offset-md-5 btn btn-primary" value="Registarse">
                    </div>
                    
                </form>
            </div>
        </div>
    </div>    
</div>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script> <!-- Se importa jQuery -->
                <script type="text/javascript"> <!-- Este script es para el uso del recaptcha -->
                    $('#store').submit(function(event) { // Se ejecuta cuando se envia el formulario
                        event.preventDefault(); // Se previene el envio del formulario
                    
                        grecaptcha.ready(function() { // Se prepara el recaptcha
                            grecaptcha.execute("{{ env('RECAPTCHA_SITE_KEY') }}", {action: 'subscribe_newsletter'}).then(function(token) { // Se ejecuta el recaptcha
                                $('#store').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">'); // Se agrega el token al formulario
                                $('#store').unbind('submit').submit(); // Se envia el formulario
                            });;
                        });
                    });
                </script>
@endsection