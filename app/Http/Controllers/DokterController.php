<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\reservasi; // Sesuaikan dengan model yang kamu pakai


class DokterController extends Controller
{
    // === FITUR PEMERIKSAAN ===
    public function pemeriksaanIndex()
    {
        // Filter untuk membuang layanan staff (grooming & hotel)
        $excludeStaff = function($query) {
            $query->where('nama_layanan', 'NOT LIKE', '%grooming%')
                  ->where('nama_layanan', 'NOT LIKE', '%hotel%')
                  ->where('nama_layanan', 'NOT LIKE', '%penitipan%');
        };

        // Ambil semua data pemeriksaan medis gabungan dengan pagination
        // Diurutkan agar status Diproses & Dikonfirmasi muncul paling atas, baru yang Selesai
        $semuaPemeriksaan = reservasi::where($excludeStaff)
            ->orderByRaw("FIELD(status, 'Diproses', 'Dikonfirmasi', 'Menunggu Jadwal', 'Selesai') ASC")
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->paginate(10); // Menampilkan 10 data per halaman biar rapi

        return view('dokter.pemeriksaan', compact('semuaPemeriksaan'));
    }

    public function pemeriksaanDetail($id) {
        $pemeriksaan = reservasi::with(['hewan', 'user'])->findOrFail($id);
        return view('dokter.pemeriksaan_detail', compact('pemeriksaan'));
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
        ]);

        $reservasi = reservasi::findOrFail($request->reservasi_id);

        // Cari data hewan_id asli biar nggak N/A
        $hewan = \App\Models\hewan::where('nama_hewan', $reservasi->pet_name)
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
            'tanggal_periksa' => now(),
        ]);

        // 3. Update status reservasi jadi Selesai
        $reservasi->status = 'Selesai';
        $reservasi->save();

        return back()->with('success', 'Rekam medis berhasil dicatat!');
    }

    // === FITUR REKAM MEDIS ===
    public function rekamMedisIndex() {
        // Tarik data asli dari tabel rekam_medis, urutkan dari yang terbaru
        $rekamMedis = \App\Models\RekamMedis::with(['user', 'hewan', 'reservasi'])->orderBy('created_at', 'desc')->get();
        return view('dokter.rekam_medis', compact('rekamMedis'));
    }

    public function rekamMedisDetail($id) {
        // Cari 1 data spesifik berdasarkan ID buat halaman detail
        $rekamMedis = \App\Models\RekamMedis::with(['user', 'hewan', 'reservasi.user'])->findOrFail($id);
        return view('dokter.rekam_medis_detail', compact('rekamMedis'));
    }

    public function rekamMedisPdf($id) {
        // Tarik data buat di-passing ke halaman PDF
        $rekamMedis = \App\Models\RekamMedis::with(['user', 'hewan', 'reservasi.user'])->findOrFail($id);

        // Panggil library dompdf
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('dokter.rekam_medis_pdf', compact('rekamMedis'));

        // Download file otomatis dengan nama yang rapi
        $namaHewan = $rekamMedis->hewan->nama_hewan ?? 'Anabul';
        return $pdf->download('Rekam-Medis-'.$namaHewan.'-'.$rekamMedis->id.'.pdf');
    }

    // === OPSI REKAM MEDIS & API ===
    public function opsiRmIndex() {
        $kategoriList = ['nafsu_makan' => 'Nafsu Makan', 'kondisi_tubuh' => 'Kondisi Tubuh']; // Contoh
        $opsiPerKategori = collect(['nafsu_makan' => collect(), 'kondisi_tubuh' => collect()]); // Contoh
        return view('dokter.opsi_rm', compact('kategoriList', 'opsiPerKategori'));
    }

    public function opsiRmApi() {
        return response()->json(['success' => true, 'data' => []]);
    }

    public function reservasiRealtime(Request $request) {
        $data = reservasi::with(['hewan', 'user'])->whereIn('status', ['Dikonfirmasi', 'Diproses'])->get();
        return response()->json(['success' => true, 'data' => $data]);
    }
}
