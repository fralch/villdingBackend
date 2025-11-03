<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#f7f7f7; margin:0; }
        .container { max-width: 420px; margin: 8vh auto; background:#fff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 18px rgba(0,0,0,.08); }
        h1 { font-size: 20px; margin: 0 0 16px; }
        .field { margin-bottom: 12px; }
        label { display:block; font-size: 13px; color:#333; margin-bottom: 6px; }
        input { width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px; }
        button { width:100%; padding:10px 12px; border:none; border-radius:6px; background:#0d6efd; color:#fff; font-weight:600; cursor:pointer; }
        button:hover { background:#0b5ed7; }
        .error { background:#ffe8e8; color:#7b0000; padding:8px 10px; border-radius:6px; margin-bottom:12px; }
        .helper { font-size:12px; color:#666; margin-top:10px; text-align:center; }
        .logout { text-align:center; margin-top:16px; }
        .logout form { display:inline-block; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
</head>
<body>
    <div class="container">
        <h1>Acceso administrador</h1>

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ url('/admin/login') }}">
            @csrf
            <div class="field">
                <label for="username">Usuario</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required autocomplete="username">
            </div>
            <div class="field">
                <label for="password">Contraseña</label>
                <input id="password" name="password" type="password" required autocomplete="current-password">
            </div>
            <button type="submit">Ingresar</button>
        </form>

        <div class="helper">Usa las credenciales definidas en <code>SIMPLE_LOGIN_USER</code> y <code>SIMPLE_LOGIN_PASSWORD</code> o las por defecto <code>admin</code>/<code>password</code>.</div>
    </div>
</body>
</html>