<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Paw Center</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>
<body class="auth-body">

    <div class="auth-card">
        <div class="auth-header">
            <h1>🐾 Paw Center</h1>
            <p>Pusat Perawatan Hewan - Masuk ke Akun Anda</p>
        </div>

        @if ($errors->any())
            <div class="error-message">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="auth-form-group">
                <label class="auth-label" for="email">Alamat Email</label>
                <div class="auth-input-wrapper">
                    <span class="auth-input-icon">✉️</span>
                    <input type="email" id="email" name="email" class="auth-input" required value="{{ old('email') }}" placeholder="budi@pawcenter.com">
                </div>
            </div>

            <div class="auth-form-group">
                <label class="auth-label" for="password">Kata Sandi</label>
                <div class="auth-input-wrapper">
                    <span class="auth-input-icon">&#x1F512;</span>
                    <input type="password" id="password" name="password" class="auth-input" required placeholder="••••••••">
                    <span class="auth-toggle-password" id="togglePassword" title="Lihat Password">👁️</span>
                </div>
            </div>

            <button type="submit" class="auth-button">Masuk ke Dashboard</button>
        </form>

        <div class="auth-card-footer">
            <p>Belum punya akun? <a href="{{ url('/register') }}">Daftar Sekarang</a></p>
            <p><a href="{{ url('/forgot-password') }}">Lupa kata sandi?</a></p>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>

</body>
</html>
