<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi | Paw Center</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>
<body class="auth-body">

    <div class="auth-card">
        <div class="auth-header">
            <h1>Lupa Kata Sandi?</h1>
            <p>Masukkan email kamu untuk mereset kata sandi.</p>
        </div>

        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error-message">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="auth-form-group">
                <label class="auth-label">Alamat Email</label>
                <div class="auth-input-wrapper">
                    <span class="auth-input-icon">✉️</span>
                    <input type="email" name="email" class="auth-input" required placeholder="email@contoh.com" value="{{ old('email') }}">
                </div>
            </div>

            <button type="submit" class="auth-button">Kirim Link Reset</button>
        </form>

        <div class="auth-card-footer">
            <p>Kembali ke <a href="{{ route('login') }}">Halaman Login</a></p>
        </div>
    </div>

</body>
</html>
