<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - D&P Pet Shop</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ungu: {
                            DEFAULT: '#7c3aed',
                            gelap: '#6d28d9',
                            tua: '#2e1065',
                            muda: '#e9d5ff',
                            terang: '#f3e8ff',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center justify-between gap-4">

                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-ungu rounded-full flex items-center justify-center">
                            <i class="fas fa-paw text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-ungu-tua leading-tight">D&P Pet Shop</h1>
                            <p class="text-xs text-gray-500">Peliharaan Sehat, Pemilik Senang</p>
                        </div>
                    </a>

                    <div class="flex-1 max-w-xl hidden sm:block">
                        <div class="relative">
                            <input
                                type="text"
                                id="searchInput"
                                placeholder="Cari produk atau layanan..."
                                class="w-full pl-10 pr-4 py-2.5 rounded-full border-2 border-ungu-muda focus:border-ungu focus:outline-none bg-ungu-terang/30 text-sm transition-colors"
                            >
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-ungu"></i>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard.checkout') }}" class="relative p-2 hover:bg-ungu-terang rounded-full transition-colors group">
                            <i class="fas fa-shopping-cart text-ungu text-xl group-hover:scale-110 transition-transform"></i>
                            <span id="cartCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                                {{ count((array) session('cart')) }}
                            </span>
                        </a>

@auth
    <div class="relative inline-block text-left" id="notifContainer">
        <button type="button" id="notifButton" class="relative p-2 hover:bg-ungu-terang rounded-full transition-colors group focus:outline-none" title="Notifikasi">
            <i class="fas fa-bell text-ungu text-xl group-hover:scale-110 transition-transform"></i>

            {{-- Badge Angka Notifikasi --}}
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-bold border-2 border-white">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
            @endif
        </button>

        <div id="notifMenu" class="hidden absolute right-0 mt-2 w-80 origin-top-right rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden z-50 transform transition-all duration-200 opacity-0 scale-95">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-800">Notifikasi Kamu</h3>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-ungu-terang text-ungu px-2 py-1 rounded-md font-semibold">
                            {{ auth()->user()->unreadNotifications->count() }} Baru
                        </span>
                        {{-- Tombol Tandai Semua Dibaca --}}
                        <a href="{{ route('notif.read_all') }}" class="text-xs text-ungu hover:text-ungu-gelap hover:underline font-medium transition-colors" title="Tandai semua dibaca">
                            <i class="fas fa-check-double mr-1"></i> Tandai Dibaca
                        </a>
                    </div>
                @endif
            </div>

            <div class="max-h-80 overflow-y-auto" id="notifListContainer">
                {{-- Ambil maksimal 50 notifikasi terbaru (gabungan yang baru & yang sudah dibaca) --}}
                @forelse(auth()->user()->notifications()->take(50)->get() as $notification)
                    <a href="{{ route('notif.read', $notification->id) }}" class="block px-4 py-3 hover:bg-ungu-terang/50 transition-colors border-b border-gray-50 last:border-0 relative notif-item {{ $notification->read_at ? 'read-notif hidden bg-gray-50/50 opacity-70' : 'bg-white' }}">
                        @if(!$notification->read_at)
                            <span class="absolute left-2 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-ungu rounded-full"></span>
                        @endif
                        <div class="pl-3">
                            <p class="text-sm leading-snug {{ !$notification->read_at ? 'text-gray-800 font-semibold' : 'text-gray-600' }}">{{ $notification->data['pesan'] }}</p>
                            <p class="text-xs text-gray-400 mt-1"><i class="far fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-8 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="far fa-bell-slash text-gray-300 text-xl"></i>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">Kosong nih!</p>
                        <p class="text-xs text-gray-400">Belum ada riwayat notifikasi untukmu.</p>
                    </div>
                @endforelse
            </div>

            {{-- Tombol Lihat Semua Riwayat (Hanya tampil jika ada notifikasi yang sudah dibaca) --}}
            @if(auth()->user()->readNotifications->count() > 0)
                <div class="bg-white border-t border-gray-100 p-2 text-center sticky bottom-0 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                    <button type="button" id="toggleAllNotif" class="text-xs font-bold text-ungu hover:text-ungu-gelap transition-colors w-full py-2 focus:outline-none rounded-lg hover:bg-ungu-terang/30">
                        <i class="fas fa-history mr-1"></i> Lihat Semua Riwayat
                    </button>
                </div>
            @endif
        </div>
    </div>
@endauth

                        @auth
    @php
        // Cek role user yang lagi login buat nentuin arah link dashboard-nya
        $dashboardRoute = route('dashboard.pelanggan'); // Default
        
        if (auth()->user()->role == 'admin') {
            $dashboardRoute = route('dashboard.admin'); // Sesuaikan sama nama route admin kamu
        } elseif (auth()->user()->role == 'owner') {
            $dashboardRoute = route('dashboard.owner');
        } elseif (auth()->user()->role == 'dokter') {
            $dashboardRoute = route('dokter.dashboard');
        } elseif (auth()->user()->role == 'staff') {
            $dashboardRoute = route('dashboard.staff');
        }
    @endphp

    <a href="{{ $dashboardRoute }}" class="p-2 hover:bg-ungu-terang rounded-full transition-colors group" title="Dashboard Saya">
        <i class="fas fa-user-circle text-ungu text-2xl group-hover:scale-110 transition-transform"></i>
    </a>
@else
    <a href="{{ route('login') }}" class="px-4 py-2 bg-ungu text-white rounded-full font-semibold hover:bg-ungu-gelap transition-colors text-sm shadow-sm">
        Login
    </a>
@endauth
                    </div>

                </div>
            </div>
        </header>

    <!-- Hero Banner -->
    <section class="bg-gradient-to-br from-ungu-tua via-[#3b0764] to-ungu-gelap py-20 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-ungu/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-ungu-gelap/10 rounded-full blur-3xl"></div>

        <a href="{{ route('home') }}" class="absolute top-4 left-4 sm:top-6 sm:left-8 inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-full text-sm backdrop-blur-md transition-all border border-white/20 z-10">
            <i class="fas fa-arrow-left text-xs"></i>
            Kembali
        </a>

        <div class="container mx-auto px-4 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4 tracking-tight">Katalog Produk & Layanan</h2>
            <div class="katalog-jump-nav">
                <a href="#produk-section" class="jump-btn">🛍️ Lihat Produk</a>
                <a href="#layanan-section" class="jump-btn">🏥 Lihat Layanan</a>
            </div>
            <p class="text-ungu-muda/80 text-lg md:text-xl max-w-2xl mx-auto">
                Temukan kebutuhan terbaik untuk hewan peliharaan kesayangan Anda dengan kualitas premium dan pelayanan sepenuh hati.
            </p>
        </div>
    </section>

    <!-- Produk Section -->
    <section class="py-12 bg-white">
        <div id="produk-section" class="container-katalog mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Produk</h3>
                    <p class="text-gray-500 mt-1">Pilihan produk berkualitas untuk peliharaan Anda</p>
                </div>
            </div>

            <div id="produkContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($produk as $p)
                <div class="search-item group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-ungu-muda transition-all duration-300 overflow-hidden" data-name="{{ $p->nama_produk }}">
                    <div class="relative overflow-hidden bg-white border-b border-gray-50 h-72 flex items-center justify-center p-4">
                        @if($p->gambar)
                            <img src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->nama_produk }}" class="w-full h-full object-contain hover:scale-105 transition-transform duration-300">
                        @else
                            <img src="https://placehold.co/300x400?text=No+Image" class="w-full h-full object-contain">
                        @endif
                    </div>
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-800 mb-1 line-clamp-2">{{ $p->nama_produk }}</h4>
                        <div class="flex items-center justify-between mt-3 mb-3">
                            <span class="text-lg font-bold text-ungu">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                        </div>

                        @auth
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $p->id }}">
                                <input type="hidden" name="tipe" value="produk">

                                <button type="submit" class="w-full mt-3 bg-ungu-terang text-ungu font-semibold py-2 rounded-xl hover:bg-ungu hover:text-white transition-all duration-300 flex items-center justify-center gap-2">
                                    <i class="fas fa-shopping-bag text-sm"></i>
                                    Masuk Keranjang
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="w-full text-center block bg-ungu-terang text-ungu font-semibold py-2 rounded-xl hover:bg-ungu hover:text-white transition-all duration-300">
                                <i class="fas fa-sign-in-alt text-sm mr-1"></i> Login untuk Membeli
                            </a>
                        @endauth
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Layanan Section -->
    <section class="py-12 bg-ungu-terang/30">
        <div id="layanan-section" class="container-katalog mx-auto px-4">
            <div class="text-center mb-10">
                <h3 class="text-2xl font-bold text-gray-800">Layanan</h3>
                <p class="text-gray-500 mt-2">Layanan profesional untuk kesehatan dan kebersihan peliharaan Anda</p>
            </div>

            <div id="layananContainer" class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            @foreach($layanan as $l)
            <div class="search-item group bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-transparent hover:border-ungu" data-name="{{ $l->nama_layanan }}">
            <div class="h-48 bg-gray-100 flex items-center justify-center relative overflow-hidden">
                @if($l->gambar)
                    <img src="{{ asset('storage/' . $l->gambar) }}" alt="{{ $l->nama_layanan }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-ungu-muda to-ungu-terang flex items-center justify-center">
                        <i class="fas fa-hand-holding-heart text-6xl text-ungu"></i>
                    </div>
                @endif
            </div>
                <div class="p-6">
                    <h4 class="text-xl font-bold text-gray-800 mb-3">{{ $l->nama_layanan }}</h4>
                    <p class="text-gray-500 text-sm mb-4 leading-relaxed">
                        {{ $l->deskripsi }}
                    </p>
                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <span class="text-xs text-gray-400">Harga</span>
                                <p class="text-2xl font-bold text-ungu">Rp {{ number_format($l->harga, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        @auth
                            <button onclick="openReservasiModal('{{ $l->nama_layanan }}', {{ $l->harga }}, {{ $l->id }})" class="w-full bg-ungu text-white font-semibold py-3 rounded-xl hover:bg-ungu-gelap transition-all duration-300 flex items-center justify-center gap-2 shadow-lg">
                                <i class="fas fa-calendar-check"></i> Reservasi
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="w-full block text-center bg-ungu text-white font-semibold py-3 rounded-xl hover:bg-ungu-gelap transition-all duration-300 shadow-lg">
                                <i class="fas fa-sign-in-alt mr-1"></i> Login untuk Reservasi
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        </div>
    </section>

    <div id="reservasiModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all scale-100">

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Reservasi Layanan</h3>
                <button onclick="closeReservasiModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>

<form id="reservasiForm" action="{{ route('reservasi.proses') }}" method="POST">
                @csrf
                <input type="hidden" name="layanan_id" id="modalLayananId">
                <input type="hidden" name="nama_layanan" id="modalNamaLayananInput">

                <div id="step-1">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Layanan</label>
                        <input type="text" id="modalLayanan" disabled class="w-full px-4 py-2 bg-ungu-terang/30 border border-ungu-muda rounded-xl text-ungu font-semibold focus:outline-none cursor-not-allowed">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga (Bayar di Klinik)</label>
                        <input type="text" id="modalHarga" disabled class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 font-semibold focus:outline-none cursor-not-allowed">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Anabul Kamu <span class="text-red-500">*</span></label>
                        <select name="nama_hewan" id="input_hewan" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-ungu focus:outline-none focus:ring-2 focus:ring-ungu-muda">
                            <option value="" disabled selected>-- Pilih Hewan Peliharaan --</option>
                            @auth
                                @forelse($hewan_user as $hewan)
                                    <option value="{{ $hewan->nama_hewan }}">{{ $hewan->nama_hewan }} ({{ $hewan->jenis_hewan }})</option>
                                @empty
                                    <option value="" disabled>Belum ada hewan. Tambah di Dashboard dulu ya!</option>
                                @endforelse
                            @endauth
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keluhan / Catatan Medis (Opsional)</label>
                        <textarea name="keluhan" rows="2" placeholder="Contoh: Kucing saya muntah-muntah sejak semalam..." class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-ungu focus:outline-none focus:ring-2 focus:ring-ungu-muda"></textarea>
                    </div>

                    <button type="button" id="btnNext" class="w-full bg-ungu text-white font-semibold py-3 rounded-xl hover:bg-ungu-gelap transition-colors flex items-center justify-center gap-2">
                        Pilih Jadwal Kunjungan <i class="fas fa-arrow-right"></i>
                    </button>
                </div>


                <div id="step-2" style="display: none;">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Reservasi / Check-in <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_reservasi" id="input_tanggal" min="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-ungu focus:outline-none focus:ring-2 focus:ring-ungu-muda">
                    </div>

                    <div class="mb-4" id="wrap_waktu">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Kunjungan <span class="text-red-500">*</span></label>
                        <select name="waktu_reservasi" id="input_waktu" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-ungu focus:outline-none focus:ring-2 focus:ring-ungu-muda">
                            <option value="" disabled selected>-- Pilih Jam --</option>
                            <option value="15:00:00">15:00 WIB</option>
                            <option value="16:00:00">16:00 WIB</option>
                            <option value="17:00:00">17:00 WIB</option>
                            <option value="18:00:00">18:00 WIB</option>
                            <option value="19:00:00">19:00 WIB</option>
                            <option value="20:00:00">20:00 WIB</option>
                            <option value="14:00:00" id="opt_hotel" style="display: none;">14:00 WIB</option>
                        </select>
                    </div>

                    <div id="wrap_hotel_info" style="display: none;">
                        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-800">
                            <p class="text-sm font-bold mb-1"><i class="fas fa-info-circle mr-1"></i> Info Pet Hotel</p>
                            <ul class="text-sm list-disc list-inside space-y-1">
                                <li><strong>Check-in:</strong> Mulai pukul 14:00 WIB</li>
                                <li><strong>Check-out:</strong> Maksimal pukul 12:00 WIB</li>
                            </ul>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Check-out <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_keluar" id="input_tgl_checkout" min="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-ungu focus:outline-none focus:ring-2 focus:ring-ungu-muda">
                            <small class="text-gray-500 mt-1 block">Tentukan kapan anabul akan dijemput pulang.</small>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" id="btnBack" class="w-1/3 bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl hover:bg-gray-300 transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </button>
                        <button type="submit" class="w-2/3 bg-ungu text-white font-semibold py-3 rounded-xl hover:bg-ungu-gelap transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-calendar-check"></i> Booking Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-6 right-6 bg-ungu text-white px-6 py-3 rounded-xl shadow-2xl transform translate-y-20 opacity-0 transition-all duration-300 z-50 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-300"></i>
        <span id="toastMessage">Berhasil ditambahkan!</span>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Tentang Kami -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-ungu rounded-full flex items-center justify-center">
                            <i class="fas fa-paw text-white text-sm"></i>
                        </div>
                        <h4 class="font-bold text-lg">D&P Pet Shop</h4>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-4">
                        D&P Pet Shop hadir untuk memenuhi kebutuhan hewan peliharaan kesayangan Anda dengan produk berkualitas dan layanan profesional.
                    </p>
                    <p class="text-gray-500 text-xs">© 2025 D&P Pets. All rights reserved.</p>
                </div>

                <!-- Layanan Kami -->
                <div>
                    <h4 class="font-bold text-lg mb-4">Layanan Kami</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-ungu-muda transition-colors">Grooming</a></li>
                        <li><a href="#" class="hover:text-ungu-muda transition-colors">Konsultasi Klinik</a></li>
                        <li><a href="#" class="hover:text-ungu-muda transition-colors">Pet Hotel</a></li>
                        <li><a href="#" class="hover:text-ungu-muda transition-colors">Pet Training</a></li>
                    </ul>
                </div>

                <!-- Hubungi Kami -->
                <div>
                    <h4 class="font-bold text-lg mb-4">Hubungi Kami</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-ungu mt-1"></i>
                            <span>Jl. Sudirman No. 123, Jakarta Selatan</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-ungu"></i>
                            <span>0812-3456-7890</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-ungu"></i>
                            <span>cs@dnppets.com</span>
                        </li>
                    </ul>
                </div>

                <!-- Ikuti Kami -->
                <div>
                    <h4 class="font-bold text-lg mb-4">Ikuti Kami</h4>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-ungu transition-colors">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-ungu transition-colors">
                            <i class="fab fa-facebook-f text-lg"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-ungu transition-colors">
                            <i class="fab fa-tiktok text-lg"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-6 text-center">
                <p class="text-gray-500 text-sm">© 2025 D&P Pets. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Search Functionality
        const searchInput = document.getElementById('searchInput');
        const searchItems = document.querySelectorAll('.search-item');

        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();

            searchItems.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                if (name.includes(searchTerm)) {
                    item.style.display = '';
                    item.style.opacity = '1';
                } else {
                    item.style.display = 'none';
                    item.style.opacity = '0';
                }
            });
        });

        // Cart Functionality
        let cartCount = 0;
        const cartCountEl = document.getElementById('cartCount');

        function addToCart(button) {
            cartCount++;
            cartCountEl.textContent = cartCount;

            // Animation
            cartCountEl.classList.add('scale-125');
            setTimeout(() => cartCountEl.classList.remove('scale-125'), 200);

            // Button feedback
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Ditambahkan';
            button.classList.remove('bg-ungu-terang', 'text-ungu');
            button.classList.add('bg-green-500', 'text-white');

            showToast('Produk berhasil masuk keranjang!');

            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.add('bg-ungu-terang', 'text-ungu');
                button.classList.remove('bg-green-500', 'text-white');
            }, 2000);
        }

        // Reservasi Modal
        const modal = document.getElementById('reservasiModal');

        // Ganti fungsi openReservasiModal menjadi ini:
        function openReservasiModal(layanan, harga, id) {
            document.getElementById('modalLayanan').value = layanan;
            document.getElementById('modalNamaLayananInput').value = layanan;
            document.getElementById('modalHarga').value = 'Rp ' + harga.toLocaleString('id-ID');
            document.getElementById('modalLayananId').value = id; // Mengisi ID hidden

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeReservasiModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function submitReservasi(e) {
            e.preventDefault();
            closeReservasiModal();
            showToast('Reservasi berhasil dibuat! Silakan cek email Anda.');
            document.getElementById('reservasiForm').reset();
        }

        // Close modal on outside click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeReservasiModal();
            }
        });

        // Toast Notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            toastMessage.textContent = message;

            toast.classList.remove('translate-y-20', 'opacity-0');

            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }

        // --- Fungsi Filter Kategori Produk ---
        function filterKategori(kategori, btn) {
            // 1. Ubah warna semua tombol kembali ke warna tidak aktif (ungu-terang)
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(b => {
                b.classList.remove('bg-ungu', 'text-white');
                b.classList.add('bg-ungu-terang', 'text-ungu');
            });

            // 2. Ubah warna tombol yang sedang diklik menjadi aktif (ungu solid)
            btn.classList.remove('bg-ungu-terang', 'text-ungu');
            btn.classList.add('bg-ungu', 'text-white');

            // 3. Filter produk yang ditampilkan
            const items = document.querySelectorAll('#produkContainer .search-item');

            items.forEach(item => {
                // Ambil nama produk dan kategori (jika ada)
                const name = item.getAttribute('data-name').toLowerCase();
                const itemKategori = item.getAttribute('data-kategori') ? item.getAttribute('data-kategori').toLowerCase() : '';

                // Logika pengecekan
                if (kategori === 'semua') {
                    // Tampilkan semua
                    item.style.display = '';
                    item.style.opacity = '1';
                } else if (itemKategori.includes(kategori) || name.includes(kategori)) {
                    // Tampilkan jika kategori atau nama mengandung kata 'makanan' atau 'aksesoris'
                    item.style.display = '';
                    item.style.opacity = '1';
                } else {
                    // Sembunyikan yang tidak cocok
                    item.style.display = 'none';
                    item.style.opacity = '0';
                }
            });
        }
    </script>

    @if(session('success'))
    <script>
        // Jalankan pop-up notifikasi otomatis saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', function() {
            showToast("{!! session('success') !!}");
        });
    </script>
    @endif

    <button id="backToTopBtn" class="btn-to-top" onclick="scrollToTop()" title="Kembali ke atas">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
    // Logic untuk buka-tutup notifikasi
    const notifButton = document.getElementById('notifButton');
    const notifMenu = document.getElementById('notifMenu');
    const notifContainer = document.getElementById('notifContainer');

    if(notifButton && notifMenu) {
        notifButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (notifMenu.classList.contains('hidden')) {
                notifMenu.classList.remove('hidden');
                setTimeout(() => {
                    notifMenu.classList.remove('opacity-0', 'scale-95');
                    notifMenu.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                notifMenu.classList.remove('opacity-100', 'scale-100');
                notifMenu.classList.add('opacity-0', 'scale-95');
                setTimeout(() => { notifMenu.classList.add('hidden'); }, 200);
            }
        });

        // Tutup kalau klik sembarang tempat di luar kotak
        document.addEventListener('click', function(e) {
            if (!notifContainer.contains(e.target) && !notifMenu.classList.contains('hidden')) {
                notifMenu.classList.remove('opacity-100', 'scale-100');
                notifMenu.classList.add('opacity-0', 'scale-95');
                setTimeout(() => { notifMenu.classList.add('hidden'); }, 200);
            }
        });
    }

    // --- Logic Tombol Back to Top ---
    const backToTopBtn = document.getElementById("backToTopBtn");

    // Ngecek posisi scroll buat nampilin/ngilangin tombol
    window.addEventListener("scroll", function() {
        if (window.scrollY > 300) {
            backToTopBtn.classList.add("show");
        } else {
            backToTopBtn.classList.remove("show");
        }
    });

    // Fungsi buat scroll mulus ke atas pas diklik
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const btnNext = document.getElementById('btnNext');
        const btnBack = document.getElementById('btnBack');
        const inputHewan = document.getElementById('input_hewan');
        const namaLayananInput = document.getElementById('modalNamaLayananInput');

        const wrapWaktu = document.getElementById('wrap_waktu');
        const inputWaktu = document.getElementById('input_waktu');
        const optHotel = document.getElementById('opt_hotel');
        const wrapHotelInfo = document.getElementById('wrap_hotel_info');
        const inputTglCheckout = document.getElementById('input_tgl_checkout');

        // LOGIC 1: Tombol NEXT (Pindah ke Halaman 2)
        btnNext.addEventListener('click', function() {
            if (!inputHewan.value) {
                alert("Pilih anabulnya dulu ya sebelum lanjut!");
                return;
            }

            step1.style.display = 'none';
            step2.style.display = 'block';

            const namaLayanan = namaLayananInput.value.toLowerCase();
            const isHotel = namaLayanan.includes('penitipan') || namaLayanan.includes('hotel');

            if (isHotel) {
                // Tampilan Khusus Pet Hotel
                wrapHotelInfo.style.display = 'block';
                inputTglCheckout.required = true;

                wrapWaktu.style.display = 'none';
                inputWaktu.required = false;
                optHotel.style.display = 'block'; // Aktifkan opsi jam 14
                inputWaktu.value = '14:00:00'; // Otomatis set jam check-in ke DB
            } else {
                // Tampilan Layanan Biasa (Grooming / Klinik)
                wrapHotelInfo.style.display = 'none';
                inputTglCheckout.required = false;

                wrapWaktu.style.display = 'block';
                inputWaktu.required = true;
                optHotel.style.display = 'none';
                if(inputWaktu.value === '14:00:00') inputWaktu.value = ''; // Reset jam
            }
        });

        // LOGIC 2: Tombol BACK (Kembali ke Halaman 1)
        btnBack.addEventListener('click', function() {
            step2.style.display = 'none';
            step1.style.display = 'block';
        });

        // LOGIC 3: Kunci Jam yang Sudah Dibooking & Min Date Checkout
        const bookedSlots = @json($bookedSlots ?? []);
        const tanggalInput = document.getElementById('input_tanggal');

        tanggalInput.addEventListener('change', function() {
            let selectedDate = this.value;
            let timeOptions = inputWaktu.querySelectorAll('option');
            const namaLayanan = namaLayananInput.value.toLowerCase();
            const isHotel = namaLayanan.includes('penitipan') || namaLayanan.includes('hotel');

            // Set minimal tanggal checkout = tanggal check-in
            inputTglCheckout.min = selectedDate;

            // Reset opsi jam
            timeOptions.forEach(opt => {
                opt.disabled = false;
                if (opt.value && opt.value !== '14:00:00') {
                    opt.text = opt.value.substring(0, 5) + " WIB";
                }
            });

            // Kunci jadwal klinik HANYA JIKA BUKAN LAYANAN HOTEL (karena hotel kandangnya banyak)
            if (bookedSlots[selectedDate] && !isHotel) {
                let takenTimes = bookedSlots[selectedDate];
                timeOptions.forEach(opt => {
                    if (opt.value && opt.value !== '14:00:00') {
                        let isBooked = takenTimes.some(time => time.startsWith(opt.value.substring(0, 5)));
                        if (isBooked) {
                            opt.disabled = true;
                            opt.text = opt.value.substring(0, 5) + " WIB (Sudah Penuh)";
                        }
                    }
                });
            }
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleAllNotifBtn = document.getElementById('toggleAllNotif');

        if (toggleAllNotifBtn) {
            toggleAllNotifBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Mencegah dropdown tertutup saat diklik

                const readNotifs = document.querySelectorAll('.read-notif');
                let isHidden = false;

                readNotifs.forEach(notif => {
                    notif.classList.toggle('hidden');
                    isHidden = notif.classList.contains('hidden');
                });

                // Ubah teks & icon tombol
                if (isHidden) {
                    toggleAllNotifBtn.innerHTML = '<i class="fas fa-history mr-1"></i> Lihat Semua Riwayat';
                } else {
                    toggleAllNotifBtn.innerHTML = '<i class="fas fa-eye-slash mr-1"></i> Sembunyikan Riwayat';
                }
            });
        }
    });
</script>
@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Nampilin pesan error ke user kalau kamarnya penuh
            alert("❌ Gagal Booking:\n\n{!! session('error') !!}");
        });
    </script>
    @endif
</body>
</html>
