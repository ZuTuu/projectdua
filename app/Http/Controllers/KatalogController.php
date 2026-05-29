<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahan wajib
use App\Models\Produk;
use App\Models\Layanan;
use App\Models\hewan; // Tambahan model hewan

class KatalogController extends Controller
{
public function index()
    {
        $produk = Produk::all();
        $layanan = Layanan::all();

        $hewan_user = [];
        if (Auth::check()) {
            $hewan_user = hewan::where('user_id', Auth::id())->get();
        }

        // [TAMBAHAN] Tarik jadwal yang sudah terisi khusus untuk dikirim ke kalender katalog
        $bookedSlots = \App\Models\reservasi::whereNotIn('status', ['Dibatalkan', 'Selesai'])
            ->get(['tanggal', 'waktu'])
            ->groupBy('tanggal')
            ->map(function ($items) {
                return $items->pluck('waktu')->toArray();
            });

        // Jangan lupa passing 'bookedSlots'
        return view('dashboard.katalog', compact('produk', 'layanan', 'hewan_user', 'bookedSlots'));
    }
}
