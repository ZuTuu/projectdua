<?php

namespace App\Http\Controllers;

use App\Models\reservasi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\PDF; // class alias from barryvdh/laravel-dompdf

class ReservasiTicketController extends Controller
{
    public function download($id)
    {
        $reservasi = reservasi::with('user')
            ->findOrFail($id);

        // Authorization: hanya pemilik reservasi
        if ($reservasi->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // E-ticket hanya saat sudah disetujui admin
        if (!in_array($reservasi->status, ['Menunggu Jadwal', 'Diproses', 'Selesai'])) {
            abort(403, 'E-ticket belum tersedia.');
        }

        /** @var PDF $pdf */
        $pdf = app('dompdf.wrapper');

        $pdf->loadView('pdf.e-ticket', [
            'reservasi' => $reservasi,
            'user' => $reservasi->user,
        ]);

        $fileName = 'ETICKET-RESERVASI-' . $reservasi->id . '.pdf';

        return $pdf->download($fileName);
    }
    public function downloadStruk($id)
    {
        // Tarik data transaksi lengkap dengan detail produk dan relasi produknya
        $transaksi = Transaksi::with(['user', 'detilProduk.produk'])->findOrFail($id);

        // Validasi keamanan: Pastikan pelanggan cuma bisa download struk miliknya sendiri
        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Struk baru bisa dicetak kalau status belanja sudah lunas/Selesai
        if ($transaksi->status !== 'Selesai') {
            abort(403, 'Struk belum tersedia karena transaksi belum selesai.');
        }

        /** @var PDF $pdf */
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.struk', [
            'transaksi' => $transaksi,
            'user' => $transaksi->user,
        ]);

        return $pdf->download('STRUK-BELANJA-' . $transaksi->id . '.pdf');
    }
}

