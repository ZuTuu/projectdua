<?php

namespace App\Http\Controllers;

use App\Models\reservasi;
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
        if ($reservasi->status !== 'Menunggu Jadwal') {
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
}

