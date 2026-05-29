<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\hewan;
use App\Models\User;
use App\Models\reservasi;
use App\Models\Produk;
use App\Models\LogAktivitas;
use App\Notifications\ReservasiNotification;
use App\Models\DetilTransaksiProduk;
use App\Models\Transaksi;
use App\Notifications\TransaksiNotification;

class DashboardController extends Controller
{
    public function pelanggan()
    {
        $userId = Auth::id();
        $petCount = \App\Models\hewan::where('user_id', $userId)->count();
        $cartCount = count(session('cart', []));

        // 1. Query khusus untuk Pet Hotel & Penitipan
        $petHotel = \App\Models\reservasi::where('user_id', $userId)
            ->where(function($query) {
                $query->where('nama_layanan', 'LIKE', '%hotel%')
                      ->orWhere('nama_layanan', 'LIKE', '%penitipan%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'hotel_page');

        // 2. Query khusus untuk Layanan Lain (Klinik / Grooming)
        $layananLain = \App\Models\reservasi::where('user_id', $userId)
            ->where(function($query) {
                $query->where('nama_layanan', 'NOT LIKE', '%hotel%')
                      ->where('nama_layanan', 'NOT LIKE', '%penitipan%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'layanan_page');

        // 3. Query untuk Pembelian Produk
        $pembelianProduk = \App\Models\Transaksi::where('user_id', $userId)
            ->with('detilProduk')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'produk_page');

        // 4. Hitung reservasi aktif (dicek admin atau disetujui) dari semua reservasi
        $activeReservationsCount = \App\Models\reservasi::where('user_id', $userId)
            ->whereIn('status', ['Menunggu Konfirmasi Admin', 'Dikonfirmasi', 'Diproses', 'Menunggu Jadwal'])
            ->count();

        // 5. Lempar semua variabel ke View
        return view('dashboard.pelanggan', compact(
            'petCount',
            'activeReservationsCount',
            'cartCount',
            'petHotel',
            'layananLain',
            'pembelianProduk'
        ));
    }

    public function layanan()
    {
        // Narik data jadwal dari database (mengabaikan status batal/selesai)
        $bookedSlots = reservasi::whereNotIn('status', ['Dibatalkan', 'Selesai'])
            ->get(['tanggal', 'waktu'])
            ->groupBy('tanggal')
            ->map(function ($items) {
                // Ubah list jam menjadi array murni
                return $items->pluck('waktu')->toArray();
            });

        // Tampilkan view form dan lempar data jadwal penuh
        return view('dashboard.layanan', compact('bookedSlots'));
    }

    public function batalkan(Request $request, $id)
    {
        $reservasi = reservasi::findOrFail($id);
        $reservasi->update([
            'status' => 'Dibatalkan',
            'alasan_batal' => $request->alasan_batal
        ]);

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Pembatalan Reservasi',
            'deskripsi' => Auth::user()->name . ' membatalkan reservasi #' . $id . ' dengan alasan: ' . $request->alasan_batal
        ]);

        return redirect()->back()->with('success', 'Reservasi berhasil dibatalkan.');
    }

    public function storeHewan(Request $request)
    {
        // 1. Ambil semua request, TAPI kecualikan umur_angka dan umur_satuan
        $data = $request->except(['umur_angka', 'umur_satuan']);

        // 2. Gabungkan angka dan satuan menjadi satu string, lalu simpan ke kolom 'umur'
        $data['umur'] = $request->umur_angka . ' ' . $request->umur_satuan;

        // 3. Pastikan user_id (pemilik hewan) otomatis terisi oleh user yang sedang login
        $data['user_id'] = Auth::id();

        $hewan = hewan::create($data);

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Tambah Hewan',
            'deskripsi' => Auth::user()->name . ' menambahkan data hewan baru: ' . $hewan->nama_hewan
        ]);

        return redirect()->back()->with('success', 'Data hewan berhasil ditambahkan.');
    }

    public function updateHewan(Request $request, $id)
    {
        $hewan = hewan::findOrFail($id);

        $data = $request->except(['umur_angka', 'umur_satuan']);

        // Kalau user update umurnya juga, kita gabungkan lagi
        if ($request->has('umur_angka') && $request->has('umur_satuan')) {
            $data['umur'] = $request->umur_angka . ' ' . $request->umur_satuan;
        }

        $hewan->update($data);

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Update Hewan',
            'deskripsi' => Auth::user()->name . ' memperbarui data hewan: ' . $hewan->nama_hewan
        ]);

        return redirect()->back()->with('success', 'Data hewan berhasil diperbarui.');
    }

    public function hapusHewan($id)
    {
        $hewan = hewan::findOrFail($id);
        $nama = $hewan->nama_hewan;
        $hewan->delete();

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Hapus Hewan',
            'deskripsi' => Auth::user()->name . ' menghapus data hewan: ' . $nama
        ]);

        return redirect()->back()->with('success', 'Data hewan berhasil dihapus.');
    }

    public function updateProfil(Request $request)
    {
        // 1. Ambil data user yang sedang login langsung dari database
        $user = User::findOrFail(auth()->id());

        // 2. Validasi semua input dari form profil.blade.php kamu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|string|min:8|confirmed' // 'confirmed' akan otomatis mengecek input 'password_confirmation'
        ]);

        // 3. Masukkan data ke model secara manual (ini bypass error $fillable)
        $user->name = $request->name;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;

        // Simpan username jika ada inputnya
        if ($request->has('username')) {
            $user->username = $request->username;
        }

        // 4. Jika user mengisi password baru, kita enkripsi (Hash) lalu simpan
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 5. Proses ganti foto profil
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama di storage jika sebelumnya sudah ada
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            // Simpan foto baru
            $user->foto_profil = $request->file('foto_profil')->store('profil', 'public');
        }

        // 6. Simpan permanen ke database!
        $user->save();

        return redirect()->back()->with('success', 'Hore! Profil kamu berhasil diperbarui.');
    }

    public function admin()
    {
        $totalPengguna = User::where('role', 'pelanggan')->count();

        // Ambil data dasar dari database
        $reservasi = reservasi::with('user')->get();
        $transaksi = Transaksi::with('user')->get();

        // Bongkar keranjang belanja produk buat ditampilin ke Admin
        foreach($transaksi as $trx) {
            $trx->detail_belanja = DetilTransaksiProduk::where('transaksi_id', $trx->id)->get();
            foreach($trx->detail_belanja as $detail) {
                $produk = Produk::find($detail->produk_id);
                $detail->nama_produk = $produk ? $produk->nama_produk : 'Produk (Dihapus)';
            }
        }

        // Gabungan data utama biar logic counter di atasnya ga rusak
        $semuaPesanan = $reservasi->concat($transaksi)->sortByDesc('created_at');

        $antreanPembayaran = $semuaPesanan->whereIn('status', ['Menunggu', 'Menunggu Pembayaran', 'Menunggu Konfirmasi Admin', 'Pending']);
        $pesananAktif = $semuaPesanan->whereIn('status', ['Dikonfirmasi', 'Diproses', 'Menunggu Jadwal', 'Menunggu Kurir', 'Pesanan Diantar', 'Selesai']);

        $menungguKonfirmasi = $antreanPembayaran->count();
        $pesananDiproses = $pesananAktif->where('status', '!=', 'Selesai')->count();

        // 1. Antrean - Layanan Klinik vs Produk Belanja
        $antreanLayanan = $antreanPembayaran->filter(function($item) {
            return $item instanceof reservasi;
        });
        $antreanProduk = $antreanPembayaran->filter(function($item) {
            return $item instanceof Transaksi;
        });

        // 2. Pesanan Aktif - Layanan Klinik vs Produk Belanja
        $aktifLayanan = $pesananAktif->filter(function($item) {
            return $item instanceof reservasi;
        });
        $aktifProduk = $pesananAktif->filter(function($item) {
            return $item instanceof Transaksi;
        });

        // Hitung pendapatan awal
        $totalPemasukanProduk = Transaksi::where('status', 'Selesai')->sum('total_harga');
        $totalPemasukanLayanan = reservasi::where('status', 'Selesai')->sum('harga_total');

        $totalPemasukan = ($totalPemasukanProduk ?? 0) + ($totalPemasukanLayanan ?? 0);

        // [PERBAIKAN] Mengubah take(10)->get() menjadi paginate(10)
        $riwayatAktivitas = LogAktivitas::with('user')->orderBy('created_at', 'desc')->paginate(10);

        $aktifProduk = $pesananAktif->filter(function($item) {
            return $item instanceof Transaksi;
        });

        // --- TAMBAHAN: Ambil data khusus Checkout Penitipan (Pet Hotel) ---
        $antreanCheckout = reservasi::with(['user'])
            ->where(function($query) {
                $query->where('nama_layanan', 'LIKE', '%hotel%')
                      ->orWhere('nama_layanan', 'LIKE', '%penitipan%');
            })
            ->whereNotNull('tanggal_keluar')
            // Ambil yang sedang in-house atau sudah dikonfirmasi
            ->whereIn('status', ['Dikonfirmasi', 'Diproses', 'Menunggu Jadwal'])
            ->orderBy('tanggal_keluar', 'asc')
            ->get();


        // Kirim variabel lama PLUS variabel baru hasil pemisahan kita
        return view('dashboard.admin', compact(
            'totalPengguna',
            'menungguKonfirmasi',
            'pesananDiproses',
            'antreanPembayaran',
            'pesananAktif',
            'riwayatAktivitas',
            'totalPemasukan',
            'totalPemasukanProduk',
            'totalPemasukanLayanan',
            'antreanLayanan',
            'antreanProduk',
            'aktifLayanan',
            'aktifProduk',
            'antreanCheckout'
        ));

    }

    public function setujuiReservasi(Request $request, $id)
    {
        // Cek apakah dia produk atau reservasi
        $pesanan = ($request->tipe == 'transaksi')
                    ? Transaksi::findOrFail($id)
                    : reservasi::findOrFail($id);

        // Jika pesanan produk dan statusnya belum 'Dikonfirmasi', kurangi stok
        if ($request->tipe == 'transaksi' && $pesanan->status !== 'Dikonfirmasi') {
            foreach ($pesanan->detilProduk as $detail) {
                $produk = Produk::find($detail->produk_id);
                if ($produk) {
                    $produk->stok -= $detail->jumlah;
                    $produk->save();
                }
            }
        }

        // Update status dan mark stok sudah dikurangi (untuk transaksi produk)
        $updateData = ['status' => 'Dikonfirmasi'];
        if ($request->tipe == 'transaksi') {
            $updateData['stok_dikurangi'] = true;
        }
        $pesanan->update($updateData);

        // Kirim notifikasi sesuai tipe pesanannya sayang
        if($request->tipe != 'transaksi'){
             $pesanan->user->notify(new ReservasiNotification($pesanan, 'Dikonfirmasi'));
        } else {
             $pesanan->user->notify(new TransaksiNotification($pesanan, 'Dikonfirmasi'));
        }

        return back()->with('success', 'Pesanan disetujui!');
    }

    public function tolakReservasi(Request $request, $id)
    {
        $pesanan = ($request->tipe == 'transaksi')
                    ? Transaksi::findOrFail($id)
                    : reservasi::findOrFail($id);

        // Jika pesanan produk dan statusnya sudah 'Dikonfirmasi' dan stok sudah dikurangi, kembalikan stok
        if ($request->tipe == 'transaksi' && $pesanan->status === 'Dikonfirmasi' && $pesanan->stok_dikurangi) {
            foreach ($pesanan->detilProduk as $detail) {
                $produk = Produk::find($detail->produk_id);
                if ($produk) {
                    $produk->stok += $detail->jumlah;
                    $produk->save();
                }
            }
        }

        // Update status dan mark stok sudah dikembalikan
        $updateData = ['status' => 'Dibatalkan'];
        if ($request->tipe == 'transaksi') {
            $updateData['stok_dikurangi'] = false;
        }

        // --- TAMBAHAN: Tangkap alasan tolak dari prompt JS ---
        if ($request->has('alasan_tolak')) {
            // PENTING: Beda tabel, beda nama kolom sayang!
            if ($request->tipe == 'transaksi') {
                $updateData['alasan_tolak'] = $request->alasan_tolak; // Tabel transaksi produk
            } else {
                $updateData['alasan_batal'] = $request->alasan_tolak; // Tabel reservasi layanan
            }
        }

        $pesanan->update($updateData);

        $pesanan->update($updateData);

        // Kirim notifikasi penolakan
        if($request->tipe != 'transaksi'){
            $pesanan->user->notify(new ReservasiNotification($pesanan, 'Ditolak'));
        } else {
            $pesanan->user->notify(new TransaksiNotification($pesanan, 'Ditolak'));
        }

        return back()->with('success', 'Pesanan ditolak.');
    }

    public function updateStatus(Request $request, $id)
    {
        // 1. JIKA TIPENYA TRANSAKSI (Produk dari Riwayat Pesanan)
        if ($request->tipe == 'transaksi') {
            $pesanan = Transaksi::findOrFail($id);
            $oldStatus = $pesanan->status;
            $newStatus = $request->status;

            // Logika otomatisasi update sirkulasi stok barang jika status berubah
            if ($oldStatus !== 'Dikonfirmasi' && $newStatus === 'Dikonfirmasi') {
                foreach ($pesanan->detilProduk as $detail) {
                    $produk = Produk::find($detail->produk_id);
                    if ($produk) {
                        $produk->stok -= $detail->jumlah;
                        $produk->save();
                    }
                }
            }

            if ($oldStatus === 'Dikonfirmasi' && $newStatus !== 'Dikonfirmasi') {
                foreach ($pesanan->detilProduk as $detail) {
                    $produk = Produk::find($detail->produk_id);
                    if ($produk) {
                        $produk->stok += $detail->jumlah;
                        $produk->save();
                    }
                }
            }

            // Simpan status baru
            $pesanan->status = $newStatus;

            // Tangkap alasan pembatalan dari form blade (alasan_tolak)
            if ($newStatus === 'Dibatalkan') {
                $pesanan->alasan_tolak = $request->alasan_tolak;
            } else {
                // Bersihkan alasan kalau admin mengubah kembali ke status selain batal
                $pesanan->alasan_tolak = null;
            }

        } else {
            // 2. JIKA TIPENYA LAYANAN/RESERVASI (Grooming / Pet Hotel)
            $pesanan = reservasi::findOrFail($id);
            $newStatus = $request->status;

            $pesanan->status = $newStatus;

            if ($newStatus === 'Dibatalkan') {
                // Model reservasi menggunakan penamaan kolom 'alasan_batal'
                $pesanan->alasan_batal = $request->alasan_tolak ?? $request->alasan_batal;
            } else {
                $pesanan->alasan_batal = null;
            }
        }

        // Simpan perubahan ke database
        $pesanan->save();
        // Kirim notif otomatis ke pelanggan
if ($request->tipe != 'transaksi') {

    if ($newStatus == 'Dibatalkan') {
        $pesanan->user->notify(
            new ReservasiNotification($pesanan, 'Ditolak')
        );
    } else {
        $pesanan->user->notify(
            new ReservasiNotification($pesanan, $newStatus)
        );
    }

} else {

    if ($newStatus == 'Dibatalkan') {
        $pesanan->user->notify(
            new TransaksiNotification($pesanan, 'Ditolak')
        );
    } else {
        $pesanan->user->notify(
            new TransaksiNotification($pesanan, $newStatus)
        );
    }

}

        return redirect()->back()->with('success', 'Status pesanan #' . $id . ' berhasil diperbarui!');
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran_dp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $reservasi = reservasi::findOrFail($id);

        if ($request->hasFile('bukti_pembayaran_dp')) {
            // Simpan file ke folder storage/app/public/bukti_dp
            $path = $request->file('bukti_pembayaran_dp')->store('bukti_dp', 'public');

            // Update status dan simpan nama filenya
            $reservasi->update([
                'bukti_pembayaran_dp' => $path,
                'status' => 'Menunggu Konfirmasi Admin'
            ]);
        }

        return redirect()->route('dashboard.pelanggan')->with('success', 'Bukti pembayaran berhasil diunggah, silakan tunggu konfirmasi admin.');
    }

    public function owner()
    {
        // Ini contoh query, sesuaikan dengan nama kolom di database kamu ya sayang
        $pendapatanHariIni = Transaksi::whereDate('created_at', today())->sum('total_harga') ?? 1450000; // Contoh fallback
        $layananSelesai = reservasi::where('status', 'Selesai')->count();
        $produkTerjual = DetilTransaksiProduk::whereHas('transaksi', function($q) {
            $q->whereDate('created_at', today());
        })->sum('jumlah') ?? 18;
        $petHotel = hewan::count(); // Contoh, sesuaikan logic pet hotel

        // Data dummy untuk grafik pendapatan 7 hari terakhir (Bisa diganti query eloquent yang di-group by tanggal)
        $chartData = [
            'labels' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            'data' => [500000, 750000, 600000, 1200000, 900000, 1500000, 1450000]
        ];

        // Data untuk tabel laporan operasional
        $laporanOperasional = Transaksi::with('user', 'detilProduk')->orderBy('created_at', 'desc')->take(10)->get();

        return view('dashboard.owner', compact(
            'pendapatanHariIni',
            'layananSelesai',
            'produkTerjual',
            'petHotel',
            'chartData',
            'laporanOperasional'
        ));
    }

// 1. Tampilan Dashboard Dokter
public function dokter()
    {
        // Filter untuk membuang layanan staff (grooming & hotel)
        $excludeStaff = function($query) {
            $query->where('nama_layanan', 'NOT LIKE', '%grooming%')
                  ->where('nama_layanan', 'NOT LIKE', '%hotel%')
                  ->where('nama_layanan', 'NOT LIKE', '%penitipan%');
        };

        // 1. Ambil SEMUA data pasien medis yang belum selesai
        $antreanAktif = reservasi::with(['user', 'hewan'])
            ->whereIn('status', ['Menunggu Jadwal', 'Dikonfirmasi', 'Diproses'])
            ->where($excludeStaff)
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->get();

        // 2. PISAHKAN antrean untuk tabel Konsultasi dan Vaksinasi
        // (Asumsi jika nama layanannya mengandung kata "vaksin", masuk ke tabel vaksinasi)
        $antreanKonsultasi = $antreanAktif->filter(function($item) {
            return stripos($item->nama_layanan, 'vaksin') === false;
        });

        $antreanVaksinasi = $antreanAktif->filter(function($item) {
            return stripos($item->nama_layanan, 'vaksin') !== false;
        });

        // 3. HITUNG Statistik / Metrik untuk kotak-kotak di atas
        $totalPemeriksaan = $antreanAktif->count();
        $totalVaksinasi = $antreanVaksinasi->count(); // INI SOLUSI ERROR KAMU
        $totalRekamMedis = reservasi::where('status', 'Selesai')->where($excludeStaff)->count();

        // (Opsional: Tetap ambil data lama untuk jaga-jaga kalau dibutuhkan)
        $jadwalLayanan = reservasi::with(['user', 'hewan'])->where($excludeStaff)->orderBy('created_at', 'desc')->take(10)->get();
        $rekamMedis = reservasi::with(['user', 'hewan', 'rekamMedis'])->where('status', 'Selesai')->where($excludeStaff)->orderBy('updated_at', 'desc')->take(5)->get();

        // 4. KIRIM SEMUA datanya ke view
        return view('dokter.dashboard', compact(
            'totalPemeriksaan',
            'totalVaksinasi',
            'totalRekamMedis',
            'antreanKonsultasi',
            'antreanVaksinasi',
            'jadwalLayanan', // bawaan kode lama
            'rekamMedis'     // bawaan kode lama
        ));
    }

    // --- TAMBAHIN DUA FUNGSI INI ---

    public function mulaiPeriksa($id)
    {
        // Cari data reservasi berdasarkan ID, lalu ubah statusnya
        $reservasi = reservasi::findOrFail($id);
        $reservasi->status = 'Diproses';
        $reservasi->save();

        return back()->with('success', 'Pasien sedang diperiksa. Status berhasil diperbarui ke Diproses!');
    }

public function simpanRM(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'reservasi_id' => 'required',
            'diagnosa'     => 'required',
            'tindakan'     => 'required',
            'nama_dokter'  => 'required', // Wajib pilih dokter dari dropdown
            'catatan'      => 'nullable',
            'biaya_tambahan' => 'nullable|numeric',
        ]);

        $reservasi = reservasi::findOrFail($request->reservasi_id);

        // Cari data hewan_id asli biar nggak N/A
        $hewan = hewan::where('nama_hewan', $reservasi->pet_name)
                                  ->where('user_id', $reservasi->user_id)
                                  ->first();

        // 2. Simpan ke tabel rekam_medis
        \App\Models\RekamMedis::create([
            'reservasi_id'    => $reservasi->id,
            'user_id'         => Auth::id(), // Akun yang login
            'nama_dokter'     => $request->nama_dokter, // 🔥 TARIK NAMA DARI DROPDOWN
            'hewan_id'        => $hewan->id ?? null,
            'diagnosa'        => $request->diagnosa,
            'tindakan'        => $request->tindakan,
            'catatan'         => $request->catatan,
            'biaya_tambahan'  => $request->biaya_tambahan,
            'tanggal_periksa' => now(),
        ]);

        // 3. Update status reservasi jadi Selesai & Update Sisa Bayar
        $biayaTambahan = $request->biaya_tambahan ?? 0;
        $reservasi->sisa_bayar = $reservasi->harga_total + $biayaTambahan - $reservasi->dp;
        $reservasi->status = 'Selesai';
        $reservasi->save();

        return back()->with('success', 'Rekam medis berhasil dicatat!');
    }
public function staff()
    {
        // 1. Ambil produk dengan stok menipis (Stok <= 5)
        $produkKritis = Produk::where('stok', '<=', 5)->get();

        // 2. Ambil data Pet Hotel (Status: Menunggu Jadwal & Diproses SAJA)
        $petHotel = reservasi::with(['user', 'hewan'])
            ->where(function($query) {
                $query->where('nama_layanan', 'LIKE', '%hotel%')
                      ->orWhere('nama_layanan', 'LIKE', '%penitipan%');
            })
            ->whereIn('status', ['Menunggu Jadwal', 'Diproses']) // Cuma yang belum selesai
            ->orderBy('id', 'desc')
            ->paginate(5, ['*'], 'hotel_page');

        // 3. Ambil data Grooming (Status: Menunggu Jadwal & Diproses SAJA)
        $grooming = reservasi::with(['user', 'hewan'])
            ->where('nama_layanan', 'LIKE', '%grooming%')
            ->whereIn('status', ['Menunggu Jadwal', 'Diproses']) // Cuma yang belum selesai
            ->orderBy('id', 'desc')
            ->paginate(5, ['*'], 'grooming_page');

        // 4. Summary (Hitung total yang AKTIF saja)
        $totalProduk = Produk::count();
        $totalLayanan = \App\Models\Layanan::count();

        // Mengambil jumlah aktif dari DB langsung biar akurat
        $jumlahHotelAktif = reservasi::where(function($q) {
                $q->where('nama_layanan', 'LIKE', '%hotel%')->orWhere('nama_layanan', 'LIKE', '%penitipan%');
            })->whereIn('status', ['Menunggu Jadwal', 'Diproses'])->count();

        $jumlahGroomingAktif = reservasi::where('nama_layanan', 'LIKE', '%grooming%')
            ->whereIn('status', ['Menunggu Jadwal', 'Diproses'])->count();

        return view('dashboard.staff', compact('produkKritis', 'petHotel', 'grooming', 'totalProduk', 'totalLayanan', 'jumlahHotelAktif', 'jumlahGroomingAktif'));
    }

    // Method baru khusus halaman Semua Stok
    public function semuaStok()
    {
        $semuaProduk = Produk::orderBy('stok', 'asc')->get();
        return view('dashboard.staff_stok', compact('semuaProduk'));
    }

    public function dataHewan()
    {
        // Ambil data hewan khusus milik pelanggan yang sedang login
        $semua_hewan = hewan::where('user_id', Auth::id())->get();

        // Bawa datanya ke halaman view
        return view('dashboard.hewan', compact('semua_hewan'));
    }

    public function profil() { return view('dashboard.profil'); }

    // Menu Riwayat Belanja Produk
    public function riwayatPesananAdmin(Request $request)
    {
        $search = $request->input('search');

        $pembelianProduk = Transaksi::with('user', 'detilProduk') // Pakai with user biar tau ini punya siapa, dan detilProduk buat tampil produknya
            ->when($search, function($query, $search) {
                return $query->where('id', 'LIKE', "%{$search}%")
                            ->orWhere('status', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.riwayat_pesanan', compact('pembelianProduk', 'search'));
    }

    // Menu Riwayat Reservasi Klinik/Layanan
    public function riwayatLayananAdmin(Request $request)
    {
        $search = $request->input('search');

        // Kita ambil data reservasi beserta data user-nya (eager loading)
        $reservasiLayanan = reservasi::with('user')
            ->when($search, function($query, $search) {
                return $query->where('id', 'LIKE', "%{$search}%")
                             ->orWhere('nama_layanan', 'LIKE', "%{$search}%")
                             ->orWhere('pet_name', 'LIKE', "%{$search}%")
                             ->orWhere('status', 'LIKE', "%{$search}%")
                             // Tambahan: Bisa cari berdasarkan nama pelanggan juga
                             ->orWhereHas('user', function($q) use ($search) {
                                 $q->where('name', 'LIKE', "%{$search}%");
                             });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15); // Admin biasanya butuh data lebih banyak per halaman, aku set 15 ya

        return view('dashboard.riwayat_layanan', compact('reservasiLayanan', 'search'));
    }

    public function rekamMedisPelanggan()
    {
        // Tarik data reservasi yang statusnya udah dikonfirmasi admin ke atas
        $riwayatMedis = reservasi::where('user_id', Auth::id())
            ->whereIn('status', ['Dikonfirmasi', 'Menunggu Jadwal', 'Diproses', 'Selesai'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('dashboard.rekam_medis_pelanggan', compact('riwayatMedis'));
    }

public function pesananGrooming()
{
    $grooming = reservasi::with(['user', 'hewan'])
        ->where('nama_layanan', 'LIKE', '%grooming%')
        ->orderBy('id', 'desc')
        ->paginate(5);

    return view('dashboard.staff_pesanan_grooming', compact('grooming'));
}

public function pesananHotel()
{
    $petHotel = reservasi::with(['user', 'hewan'])
        ->where(function($query) {
            $query->where('nama_layanan', 'LIKE', '%hotel%')
                  ->orWhere('nama_layanan', 'LIKE', '%penitipan%');
        })
        ->orderBy('id', 'desc')
        ->paginate(5);

    return view('dashboard.staff_pesanan_hotel', compact('petHotel'));
}
}
