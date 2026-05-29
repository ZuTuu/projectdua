<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | Paw Center</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>
<body class="auth-body">

    <div class="auth-card">
        <div class="auth-header">
            <h1>Daftar Akun Baru</h1>
            <p>Bergabunglah dengan Paw Center untuk layanan terbaik</p>
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

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="auth-form-group">
                <label class="auth-label" for="name">Nama Lengkap</label>
                <div class="auth-input-wrapper">
                    <span class="auth-input-icon">👤</span>
                    <input type="text" id="name" name="name" class="auth-input" required value="{{ old('name') }}" placeholder="Budi Santoso">
                </div>
            </div>

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
                    <span class="auth-toggle-password" onclick="toggleVisibility('password', this)" title="Lihat Password">👁️</span>
                </div>
            </div>

            <div class="auth-form-group">
                <label class="auth-label" for="password-confirm">Konfirmasi Kata Sandi</label>
                <div class="auth-input-wrapper">
                    <span class="auth-input-icon">&#x1F512;</span>
                    <input type="password" id="password-confirm" name="password_confirmation" class="auth-input" required placeholder="••••••••">
                    <span class="auth-toggle-password" onclick="toggleVisibility('password-confirm', this)" title="Lihat Password">👁️</span>
                </div>
            </div>
            <button type="submit" class="auth-button">Daftar Sekarang</button>
        </form>
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-ungu-gelap font-semibold hover:underline">
                    Login di sini
                </a>
            </p>
        </div>
    </div>

    <script>
    function toggleVisibility(inputId, iconElement) {
        const passwordInput = document.getElementById(inputId);
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        iconElement.textContent = type === 'password' ? '👁️' : '🙈';
    }
    </script>

</body>
</html>
