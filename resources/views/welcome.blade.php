<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paw Center - D&F Pet Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .sub-judul {
            text-align: center; color: #36005E; font-size: 1.3rem; margin: 40px 0 20px; font-weight: 600;
        }

        /* Memaksa area tombol auth agar tidak hilang atau tertutup */
        .auth-btns {
            display: flex !important;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body class="welcome-page">

    <header class="welcome-header">
        <div class="container">
            <div class="logo-area">🐾 Paw Center</div>
            <nav>
                <ul class="nav-links">
                    <li><a href="#beranda">Beranda</a></li>
                    <li><a href="#tentang">Tentang Kami</a></li>
                    <li><a href="#layanan">Layanan</a></li>
                    <li><a href="#produk">Produk</a></li>
                    <li><a href="#dokter">Dokter</a></li>
                    <li><a href="#kontak">Kontak</a></li>
                </ul>
            </nav>
            <div class="auth-btns">
                @if(Auth::check())
                    @php
                        $role = Auth::user()->role ?? 'pelanggan';
                        $route = 'dashboard.pelanggan';
                        if(in_array($role, ['owner', 'admin'])) $route = 'dashboard.admin';
                        elseif(in_array($role, ['kasir', 'staff'])) $route = 'dashboard.staff';
                        elseif($role == 'dokter') $route = 'dashboard.dokter';
                    @endphp
                    <a href="{{ route($route) }}" class="btn btn-dark" style="border-radius: 25px; padding: 8px 20px;">Dashboard</a>

                @else
                    <a href="{{ url('/login') }}" class="btn btn-dark" style="border-radius: 25px; padding: 8px 20px;">Masuk</a>
                    <a href="{{ url('/register') }}" class="btn btn-outline" style="border-radius: 25px; padding: 8px 20px; border: 1px solid #36005E;">Daftar</a>

                @endif
            </div>
        </div>
    </header>

    <section class="hero" id="beranda">
        <div class="container">
            <div class="hero-text">
                <div class="hero-tag">Layanan Kesehatan Hewan Terbaik</div>
                <h1 class="hero-title">Jadwalkan Kunjungan & Penuhi Kebutuhan Hewan Peliharaan Kesayangan Anda di D&F Pet Shop</h1>
                <p class="hero-desc">Sistem terpadu layanan reservasi klinik, rekam medis, pet hotel, dan e-commerce terlengkap di Subang.</p>
                </div>
            <div class="hero-img-container" style="background-image: url('{{ asset('storage/welcome/hero.jpg') }}');"></div>
        </div>
    </section>

    <section class="profil" id="tentang">
        <div class="container">
            <h2 class="section-title">Profil D&F Petshop</h2>
            <div class="profil-content">
                <div class="profil-img" style="background-image: url('{{ asset('storage/welcome/profil.jpg') }}');"></div>
                <div class="profil-text">
                    <p>Kami memahami bahwa hewan peliharaan adalah bagian dari keluarga Anda. Oleh karena itu, kami berkomitmen untuk memberikan perawatan medis dengan penuh kasih sayang dan profesional.</p>
                    <ul>
                    <li><span style="background:#36005E; color:white; border-radius:50%; width:20px; height:20px; display:inline-block; text-align:center; line-height:20px; font-size:12px;">✓</span> Dokter Hewan Berpengalaman</li>

                    <li><span style="background:#36005E; color:white; border-radius:50%; width:20px; height:20px; display:inline-block; text-align:center; line-height:20px; font-size:12px;">✓</span> Fasilitas Medis Modern</li>

                    <li><span style="background:#36005E; color:white; border-radius:50%; width:20px; height:20px; display:inline-block; text-align:center; line-height:20px; font-size:12px;">✓</span> Pet Shop Terlengkap</li>

                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="lokasi">
        <div class="container">
            <h2 class="section-title" style="margin-bottom: 10px;">Lokasi D&F</h2>
            <p class="lokasi-desc">D&F Pet Shop memiliki dua cabang di Subang, siap melayani berbagai kebutuhan hewan peliharaan kesayangan Anda.</p>
<div class="maps-grid">
                <a href="https://www.google.com/maps/search/?api=1&query=Jl.+DI.Panjaitan+No.24,+Soklat,+Kec.+Subang,+Kabupaten+Subang" target="_blank" class="map-card">
                    <div class="map-img" style="background-image: url('{{ asset('storage/lokasi/subang.jpg') }}');"></div>
                    <div class="map-label">D&F Pet Shop & Clinic Cabang 1<br>
                        <span style="font-size:12px; font-weight:normal; color:var(--text-muted);">Jl. DI.Panjaitan No.24, Soklat, Kec. Subang</span>
                    </div>
                </a>
                <a href="https://www.google.com/maps/search/?api=1&query=Jl.+Ion+Martasasmita+No.3,+Rancasari,+Kec.+Pamanukan,+Kabupaten+Subang" target="_blank" class="map-card">
                    <div class="map-img" style="background-image: url('{{ asset('storage/lokasi/pagaden.jpg') }}');"></div>
                    <div class="map-label">D&F Pet Shop Cabang 2<br>
                        <span style="font-size:12px; font-weight:normal; color:var(--text-muted);">Jl. Ion Martasasmita No.3, Rancasari, Kec. Pamanukan</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

<section id="layanan">
    <div class="container">
        <h2 class="sub-judul">Layanan Kami</h2>
        <div class="layanan-grid">
            @foreach($layanan as $item)
                <div class="layanan-card">
                    <img src="{{ $item->gambar ? asset('storage/' . $item->gambar) : asset('images/default-layanan.jpg') }}" alt="{{ $item->nama_layanan }}">
                    <h3>{{ $item->nama_layanan }}</h3>
                    <p>{{ Str::limit($item->deskripsi, 100) }}</p>
                    <p class="harga">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="produk">
    <div class="container">
        <h2 class="sub-judul">Produk Unggulan</h2>
        <div class="produk-grid">
            @foreach($produk as $p)
                <div class="produk-card">
                    <img src="{{ $p->gambar ? asset('storage/' . $p->gambar) : asset('images/default-produk.jpg') }}" alt="{{ $p->nama_produk }}">
                    <h3>{{ $p->nama_produk }}</h3>
                    <p class="harga">Rp {{ number_format($p->harga, 0, ',', '.') }}</p>
                    <p>Stok: {{ $p->stok }}</p>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-5">
        <a href="{{ url('/katalog') }}" class="btn btn-primary">
            Lihat Semua Produk
        </a>
    </div>
    </div>
</section>

    <section class="dokter" id="dokter">
        <div class="container dokter-container">
            <h2 class="section-title">Dokter Kami</h2>
            <div class="dokter-row">
                <div class="dokter-img" style="background-image: url('{{ asset('storage/dokter/dokter1.jpg') }}');"></div>
                <div class="dokter-info">
                    <h3>drh. Arifa Nurjayanti</h3>
                    <span>Dokter Hewan Kecil</span>
                    <p>Menjadi dokter sekaligus pemilik di D&F Pet Shop & Clinic sejak 2017 hingga saat ini.</p>
                </div>
            </div>
            <div class="dokter-row reverse">
                <div class="dokter-img" style="background-image: url('{{ asset('storage/dokter/dokter2.jpg') }}'); background-position: top;"></div>
                <div class="dokter-info">
                    <h3>drh. Aditya Lanang W.</h3>
                    <span>Dokter Hewan Eksotik</span>
                    <p>Menjadi dokter D&F sejak 2019 dan mengelola cabang kedua untuk penanganan hewan khusus.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="faq">
        <div class="container faq-container">
            <h2 class="section-title">Tanyakan Kepada Kami</h2>
            <details>
                <summary>Bagaimana cara jadwalkan layanan grooming & klinik?</summary>
                <div class="faq-content">Anda dapat masuk melalui dashboard pelanggan setelah login. Anda bebas memilih tanggal dan jam operasional dokter.</div>
            </details>
            <details>
                <summary>Apakah saya dapat langsung membeli produk tanpa login?</summary>
                <div class="faq-content">Tidak, untuk proses checkout otomatis disarankan untuk login terlebih dahulu ke akun Paw Center.</div>
            </details>
            <details>
                <summary>Berapa biaya konsultasi dengan dokter?</summary>
                <div class="faq-content">Biaya konsultasi dasar mulai dari Rp. 50.000. Belum termasuk tindakan khusus atau obat-obatan.</div>
            </details>
        </div>
    </section>

    <section class="contact" id="kontak" style="padding: 80px 0; text-align: center;">
        <div class="container">
            <h2 class="section-title" style="margin-bottom: 15px; color: var(--purple-900);">Hubungi Kami Sekarang</h2>
            <p style="color: var(--text-muted); font-size: 15px; max-width: 600px; margin: 0 auto 30px; line-height: 1.6;">
                Jika Anda memiliki pertanyaan seputar layanan klinik, stok produk pet shop, atau butuh tindakan darurat, silakan hubungi kami melalui WhatsApp.
            </p>
            <a href="https://wa.me/6281223372450" target="_blank" class="btn" style="background-color: #36005E; color: #ffffff; border: 1px solid #2c004f; border-radius: 30px; padding: 12px 30px; font-weight: 500; text-decoration: none; display: inline-block;">

                Hubungi Via WhatsApp
            </a>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Tentang Kami</h4>
                    <p>D&F Petshop adalah klinik dan pet shop terpercaya di Subang, Jawa Barat, yang menyediakan layanan kesehatan hewan terpadu.</p>
                </div>
                <div class="footer-col">
                    <h4>Layanan Kami</h4>
                    <ul>
                        <li>Klinik Hewan</li>
                        <li>Vaksinasi & Sterilisasi</li>
                        <li>Pet Hotel</li>
                        <li>Grooming Hewan</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Hubungi Kami</h4>
                    <ul>
                        <li>📍 Jl. DI.Panjaitan No.24, Soklat, Kec. Subang, Kab. Subang, Jawa Barat </li>
                        <li>📍 Jl. Ion Martasasmita No. 3, Rancasari, Kec. Pamanukan, Kab. Subang, Jawa Barat</li>
                        <li>📞 0812-2337-2450</li>
                        <li>✉️ cs@dnfpetshop.com</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                © 2026 Paw Center by ALTALOGI. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
