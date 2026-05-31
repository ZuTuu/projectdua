<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetilTransaksiProduk;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PosKasirController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');

        $produks = Produk::when($query, function ($q, $query) {
            $q->where('nama_produk', 'like', '%' . $query . '%')
                ->orWhere('kategori', 'like', '%' . $query . '%');
        })
            ->orderBy('nama_produk')
            ->limit(200)
            ->get();

        return view('dashboard.admin.pos', compact('produks', 'query'));
    }

    public function checkout(Request $request)
    {
        // Parse JSON kalau form POS ngirimnya pakai items_json
        if ($request->filled('items_json') && is_string($request->items_json) && empty($request->items)) {
            $parsed = json_decode($request->input('items_json'), true);
            if (!is_array($parsed)) {
                return back()->withInput()->with('error', 'Format items POS tidak valid.');
            }
            $request->merge(['items' => $parsed]);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|integer|exists:produk,id',
            'items.*.qty' => 'required|integer|min:1|max:999',
            'payment_method' => 'required|in:cash,transfer',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Ambil data produk buat validasi harga aktual
        $produkIds = collect($request->items)->pluck('produk_id')->all();
        $produks = Produk::whereIn('id', $produkIds)->get()->keyBy('id');

        $totalHarga = 0;
        $detailRows = [];

        foreach ($request->items as $row) {
            $produkId = (int)$row['produk_id'];
            $qty = (int)$row['qty'];

            $produk = $produks->get($produkId);
            if (!$produk) {
                return back()->withInput()->with('error', 'Ada produk yang tidak ditemukan.');
            }

            $hargaSatuan = (float)$produk->harga;
            $subtotal = $hargaSatuan * $qty;
            $totalHarga += $subtotal;

            $detailRows[] = [
                'produk_id' => $produkId,
                'jumlah' => $qty,
                'harga_satuan' => $hargaSatuan,
            ];
        }

        $buktiPath = null;

        // Setup status awal sesuai pembayaran
        $statusAwal = 'Selesai';
        if ($request->payment_method === 'transfer') {
            $statusAwal = 'Menunggu Konfirmasi Admin';
            if ($request->hasFile('bukti_bayar')) {
                $buktiPath = $request->file('bukti_bayar')->store('bukti_pembayaran', 'public');
            }
        }

        // Pakai Try-Catch biar aplikasi gak crash kalau Exception ke-trigger
        try {
            DB::transaction(function () use ($totalHarga, $statusAwal, $buktiPath, $detailRows) {
                // Lock row produk supaya stok konsisten
                $produkIds = collect($detailRows)->pluck('produk_id')->unique()->values()->all();
                $produksLocked = Produk::whereIn('id', $produkIds)->lockForUpdate()->get()->keyBy('id');

                // Validasi stok & kurangi stok
                foreach ($detailRows as $d) {
                    $produk = $produksLocked->get((int)$d['produk_id']);
                    if (!$produk) {
                        throw new \Exception('Produk tidak ditemukan saat update stok.');
                    }

                    $qty = (int)$d['jumlah'];

                    if ((int)$produk->stok < $qty) {
                        throw new \Exception('Stok produk "' . $produk->nama_produk . '" tidak cukup!');
                    }

                    $produk->stok = (int)$produk->stok - $qty;
                    $produk->save();
                }

                // Buat transaksi + detail
                $transaksi = Transaksi::create([
                    'user_id' => Auth::id(),
                    'total_harga' => $totalHarga,
                    'status' => $statusAwal,
                    'bukti_pembayaran' => $buktiPath,
                ]);

                // Insert child record satu per satu (Pengganti sync)
                foreach ($detailRows as $d) {
                    DetilTransaksiProduk::create([
                        'transaksi_id' => $transaksi->id,
                        'produk_id' => $d['produk_id'],
                        'jumlah' => $d['jumlah'],
                        'harga_satuan' => $d['harga_satuan'],
                    ]);
                }
            });

            return redirect()->route('admin.transaksi.index')->with('success', 'Transaksi POS berhasil dibuat.');

        } catch (\Exception $e) {
            // Kalau ada error (kayak stok habis), balikin pesannya ke halaman POS
            return back()->withInput()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }
}
