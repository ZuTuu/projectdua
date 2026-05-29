<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Kata Sandi | Paw Center</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>
<body class="auth-body">

    <div class="auth-card">
        <div class="auth-header">
            <h1>Ganti Kata Sandi</h1>
            <p>Silakan masukkan password baru untuk akun kamu.</p>
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

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="auth-form-group">
                <label class="auth-label">Alamat Email</label>
                <div class="auth-input-wrapper">
                    <span class="auth-input-icon">✉️</span>
                    <input type="email" name="email" class="auth-input" required placeholder="email@contoh.com" value="{{ old('email', $email ?? '') }}">
                </div>
            </div>

            <div class="auth-form-group">
                <label class="auth-label">Password Baru</label>
                <div class="auth-input-wrapper">
                    <span class="auth-input-icon">&#x1F512;</span>
                    <input type="password" id="password" name="password" class="auth-input" required placeholder="Minimal 8 karakter">
                    <span class="auth-toggle-password" id="togglePassword" title="Lihat Password">👁️</span>
                </div>
            </div>

            <div class="auth-form-group">
                <label class="auth-label">Konfirmasi Password Baru</label>
                <div class="auth-input-wrapper">
                    <span class="auth-input-icon">&#x1F512;</span>
                    <input type="password" id="password-confirm" name="password_confirmation" class="auth-input" required placeholder="Ulangi password baru">
                    <span class="auth-toggle-password" id="togglePasswordConfirm" title="Lihat Password">👁️</span>
                </div>
            </div>

            <button type="submit" class="auth-button">Update Password</button>
        </form>
    </div>

    <script>
        // JS Untuk Toggle Password Baru
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });

        // JS Untuk Toggle Konfirmasi Password Baru
        const togglePasswordConfirm = document.querySelector('#togglePasswordConfirm');
        const confirmInput = document.querySelector('#password-confirm');

        togglePasswordConfirm.addEventListener('click', function () {
            const type = confirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmInput.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>
</body>
</html>
