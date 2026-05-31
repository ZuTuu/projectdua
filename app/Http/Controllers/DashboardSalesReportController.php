<?php

namespace App\Http\Controllers;

use App\Models\DetilTransaksiProduk;
use App\Models\reservasi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardSalesReportController extends Controller
{
    // Dipakai untuk dashboard admin/kasir: laporan penjualan produk + layanan
    public function adminLaporanPenjualan(Request $request)
    {
        $mode = $request->query('mode', 'day'); // day|month|year
        $count = (int) $request->query('count', 30);

        // Default sesuai request: day=30, month=12, year=5
        if ($mode === 'month' && !in_array($count, [6, 12], true)) {
            $count = 12;
        }
        if ($mode === 'year' && !in_array($count, [5], true)) {
            $count = 5;
        }
        if ($mode === 'day' && !in_array($count, [7, 14, 30], true)) {
            $count = 30;
        }

        $now = now();

        $revenueLabels = [];
        $revenueData = [];

        $topProducts = collect();
        $pieLabels = [];
        $pieData = [];

        $transactionsTable = collect();

        $totalRevenue = 0;
        $totalQtySold = 0;
        $topProductName = null;
        
        $pemasukkanProduk = 0;
        $pemasukkanLayanan = 0;
        
        // Data untuk layanan
        $totalLayananTerjual = 0;
        $topServiceName = null;
        $topServices = collect();
        $serviceLabels = [];
        $serviceData = [];
        $comparisonLabels = ['Produk', 'Layanan'];
        $comparisonData = [0, 0];

        if ($mode === 'day') {
            $start = $now->copy()->subDays($count - 1)->startOfDay();
            $end = $now->copy()->endOfDay();

            $produkDaily = Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as dt, SUM(total_harga) as total')
                ->groupBy('dt')
                ->pluck('total', 'dt');

            $layananDaily = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as dt, SUM(harga_total) as total')
                ->groupBy('dt')
                ->pluck('total', 'dt');

            for ($i = 0; $i < $count; $i++) {
                $dt = $start->copy()->addDays($i)->toDateString();
                $value = (float)($produkDaily[$dt] ?? 0) + (float)($layananDaily[$dt] ?? 0);

                $revenueLabels[] = $start->copy()->addDays($i)->format('d M');
                $revenueData[] = $value;
                $totalRevenue += $value;
            }

            $topProductsAgg = DetilTransaksiProduk::query()
                ->join('transaksi', 'transaksi.id', '=', 'detil_transaksi_produk.transaksi_id')
                ->join('produk', 'produk.id', '=', 'detil_transaksi_produk.produk_id')
                ->where('transaksi.status', 'Selesai')
                ->whereBetween('transaksi.created_at', [$start, $end])
                ->select('produk.nama_produk')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah) as qty_sold')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah * detil_transaksi_produk.harga_satuan) as revenue_est')
                ->groupBy('produk.nama_produk')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topProducts = $topProductsAgg->map(function ($row) {
                return (object) [
                    'nama_produk' => $row->nama_produk,
                    'qty_sold' => (int) $row->qty_sold,
                    'revenue_est' => (float) ($row->revenue_est ?? 0),
                ];
            });

            $totalQtySold = (int) $topProductsAgg->sum('qty_sold');
            if ($topProducts->count() > 0) {
                $topProductName = $topProducts->first()->nama_produk;
            }

            $pieLabels = $topProducts->pluck('nama_produk')->all();
            $pieData = $topProducts->pluck('qty_sold')->map(fn($v) => (int) $v)->all();
            
            $pemasukkanProduk = (float) Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga');
            
            $pemasukkanLayanan = (float) reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('harga_total');

            // Top Layanan untuk mode day
            $topServicesAgg = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->select('nama_layanan')
                ->selectRaw('COUNT(*) as qty_sold')
                ->selectRaw('SUM(harga_total) as revenue_est')
                ->groupBy('nama_layanan')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topServices = $topServicesAgg->map(function ($row) {
                return (object) [
                    'nama_layanan' => $row->nama_layanan,
                    'qty_sold' => (int) $row->qty_sold,
                    'revenue_est' => (float) ($row->revenue_est ?? 0),
                ];
            });

            $totalLayananTerjual = (int) $topServicesAgg->sum('qty_sold');
            if ($topServices->count() > 0) {
                $topServiceName = $topServices->first()->nama_layanan;
            }

            $serviceLabels = $topServices->pluck('nama_layanan')->all();
            $serviceData = $topServices->pluck('qty_sold')->map(fn($v) => (int) $v)->all();
            
            // Data untuk grafik perbandingan produk vs layanan
            $comparisonData = [(float) $pemasukkanProduk, (float) $pemasukkanLayanan];

            $transactionsTable = Transaksi::with('user')
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        if ($mode === 'month') {
            $start = $now->copy()->subMonths($count - 1)->startOfMonth();
            $end = $now->copy()->endOfMonth();

            $produkMonthly = Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(total_harga) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym');

            $layananMonthly = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(harga_total) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym');

            for ($i = 0; $i < $count; $i++) {
                $ms = $start->copy()->addMonths($i);
                $ym = $ms->format('Y-m');

                $value = (float)($produkMonthly[$ym] ?? 0) + (float)($layananMonthly[$ym] ?? 0);

                $revenueLabels[] = $ms->format('M Y');
                $revenueData[] = $value;
                $totalRevenue += $value;
            }

            $topProductsAgg = DetilTransaksiProduk::query()
                ->join('transaksi', 'transaksi.id', '=', 'detil_transaksi_produk.transaksi_id')
                ->join('produk', 'produk.id', '=', 'detil_transaksi_produk.produk_id')
                ->where('transaksi.status', 'Selesai')
                ->whereBetween('transaksi.created_at', [$start, $end])
                ->select('produk.nama_produk')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah) as qty_sold')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah * detil_transaksi_produk.harga_satuan) as revenue_est')
                ->groupBy('produk.nama_produk')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topProducts = $topProductsAgg->map(function ($row) {
                return (object) [
                    'nama_produk' => $row->nama_produk,
                    'qty_sold' => (int) $row->qty_sold,
                    'revenue_est' => (float) ($row->revenue_est ?? 0),
                ];
            });

            $totalQtySold = (int) $topProductsAgg->sum('qty_sold');
            if ($topProducts->count() > 0) {
                $topProductName = $topProducts->first()->nama_produk;
            }

            $pieLabels = $topProducts->pluck('nama_produk')->all();
            $pieData = $topProducts->pluck('qty_sold')->map(fn($v) => (int) $v)->all();

            $transactionsTable = Transaksi::with('user')
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
            
            $pemasukkanProduk = (float) Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga');
            
            $pemasukkanLayanan = (float) reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('harga_total');
            
            // Top Layanan untuk mode month
            $topServicesAgg = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->select('nama_layanan')
                ->selectRaw('COUNT(*) as qty_sold')
                ->selectRaw('SUM(harga_total) as revenue_est')
                ->groupBy('nama_layanan')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topServices = $topServicesAgg->map(function ($row) {
                return (object) [
                    'nama_layanan' => $row->nama_layanan,
                    'qty_sold' => (int) $row->qty_sold,
                    'revenue_est' => (float) ($row->revenue_est ?? 0),
                ];
            });

            $totalLayananTerjual = (int) $topServicesAgg->sum('qty_sold');
            if ($topServices->count() > 0) {
                $topServiceName = $topServices->first()->nama_layanan;
            }

            $serviceLabels = $topServices->pluck('nama_layanan')->all();
            $serviceData = $topServices->pluck('qty_sold')->map(fn($v) => (int) $v)->all();
            
            // Data untuk grafik perbandingan produk vs layanan
            $comparisonData = [(float) $pemasukkanProduk, (float) $pemasukkanLayanan];
        }

        if ($mode === 'year') {
            $start = $now->copy()->subYears($count - 1)->startOfYear();
            $end = $now->copy()->endOfYear();

            $produkYearly = Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEAR(created_at) as y, SUM(total_harga) as total')
                ->groupBy('y')
                ->pluck('total', 'y');

            $layananYearly = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEAR(created_at) as y, SUM(harga_total) as total')
                ->groupBy('y')
                ->pluck('total', 'y');

            for ($i = 0; $i < $count; $i++) {
                $y = (int) $start->copy()->addYears($i)->format('Y');

                $value = (float)($produkYearly[$y] ?? 0) + (float)($layananYearly[$y] ?? 0);

                $revenueLabels[] = (string) $y;
                $revenueData[] = $value;
                $totalRevenue += $value;
            }

            $topProductsAgg = DetilTransaksiProduk::query()
                ->join('transaksi', 'transaksi.id', '=', 'detil_transaksi_produk.transaksi_id')
                ->join('produk', 'produk.id', '=', 'detil_transaksi_produk.produk_id')
                ->where('transaksi.status', 'Selesai')
                ->whereBetween('transaksi.created_at', [$start, $end])
                ->select('produk.nama_produk')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah) as qty_sold')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah * detil_transaksi_produk.harga_satuan) as revenue_est')
                ->groupBy('produk.nama_produk')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topProducts = $topProductsAgg->map(function ($row) {
                return (object) [
                    'nama_produk' => $row->nama_produk,
                    'qty_sold' => (int) $row->qty_sold,
                    'revenue_est' => (float) ($row->revenue_est ?? 0),
                ];
            });

            $totalQtySold = (int) $topProductsAgg->sum('qty_sold');
            if ($topProducts->count() > 0) {
                $topProductName = $topProducts->first()->nama_produk;
            }

            $pieLabels = $topProducts->pluck('nama_produk')->all();
            $pieData = $topProducts->pluck('qty_sold')->map(fn($v) => (int) $v)->all();

            $transactionsTable = Transaksi::with('user')
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
            
            $pemasukkanProduk = (float) Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga');
            
            $pemasukkanLayanan = (float) reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('harga_total');
            
            // Top Layanan untuk mode year
            $topServicesAgg = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->select('nama_layanan')
                ->selectRaw('COUNT(*) as qty_sold')
                ->selectRaw('SUM(harga_total) as revenue_est')
                ->groupBy('nama_layanan')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topServices = $topServicesAgg->map(function ($row) {
                return (object) [
                    'nama_layanan' => $row->nama_layanan,
                    'qty_sold' => (int) $row->qty_sold,
                    'revenue_est' => (float) ($row->revenue_est ?? 0),
                ];
            });

            $totalLayananTerjual = (int) $topServicesAgg->sum('qty_sold');
            if ($topServices->count() > 0) {
                $topServiceName = $topServices->first()->nama_layanan;
            }

            $serviceLabels = $topServices->pluck('nama_layanan')->all();
            $serviceData = $topServices->pluck('qty_sold')->map(fn($v) => (int) $v)->all();
            
            // Data untuk grafik perbandingan produk vs layanan
            $comparisonData = [(float) $pemasukkanProduk, (float) $pemasukkanLayanan];
        }

        $modeDisplay = [
            'year' => 'Tahunan',
        ][$mode] ?? $mode;

        return view('dashboard.admin.laporan', [
            'mode' => $mode,
            'count' => $count,
            'modeDisplay' => $modeDisplay,
            'totalRevenue' => $totalRevenue,
            'totalQtySold' => $totalQtySold,
            'topProductName' => $topProductName,
            'revenueLabels' => $revenueLabels,
            'revenueData' => $revenueData,
            'pieLabels' => $pieLabels,
            'pieData' => $pieData,
            'topProducts' => $topProducts,
            'transactionsTable' => $transactionsTable,
            'pemasukkanProduk' => $pemasukkanProduk,
            'pemasukkanLayanan' => $pemasukkanLayanan,
            'totalLayananTerjual' => $totalLayananTerjual,
            'topServiceName' => $topServiceName,
            'topServices' => $topServices,
            'serviceLabels' => $serviceLabels,
            'serviceData' => $serviceData,
            'comparisonLabels' => $comparisonLabels,
            'comparisonData' => $comparisonData,
        ]);
    }

    public function ownerLaporanOperasional(Request $request)
    {
        $mode = $request->query('mode', 'day');
        $count = (int) $request->query('count', 30);

        if ($mode === 'month' && !in_array($count, [6, 12], true)) {
            $count = 12;
        }
        if ($mode === 'year' && !in_array($count, [5], true)) {
            $count = 5;
        }
        if ($mode === 'day' && !in_array($count, [7, 14, 30], true)) {
            $count = 30;
        }

        $now = now();

        $revenueLabels = [];
        $revenueData = [];
        $totalRevenue = 0.0;

        $topProductsAgg = collect();
        $topProducts = collect();
        $pieLabels = [];
        $pieData = [];
        $topProductName = null;

        $topLayanan = collect();
        $topLayananName = null;
        $topLayananPieLabels = [];
        $topLayananPieData = [];

        $transactionsTable = collect();
        $totalQtySold = 0;

        $totalTransaksiSelesai = 0;
        $totalLayananSelesai = 0;
        $omzetBersih = 0.0;
        $avgOrderValue = 0.0;
        $statusKonversiCount = 0;

        if ($mode === 'day') {
            $start = $now->copy()->subDays($count - 1)->startOfDay();
            $end = $now->copy()->endOfDay();

            $produkDaily = Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as dt, SUM(total_harga) as total')
                ->groupBy('dt')
                ->pluck('total', 'dt');

            $layananDaily = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as dt, SUM(harga_total) as total')
                ->groupBy('dt')
                ->pluck('total', 'dt');

            for ($i = 0; $i < $count; $i++) {
                $dt = $start->copy()->addDays($i)->toDateString();
                $value = (float)($produkDaily[$dt] ?? 0) + (float)($layananDaily[$dt] ?? 0);

                $revenueLabels[] = $start->copy()->addDays($i)->format('d M');
                $revenueData[] = $value;
                $totalRevenue += $value;
            }

            $topProductsAgg = DetilTransaksiProduk::query()
                ->join('transaksi', 'transaksi.id', '=', 'detil_transaksi_produk.transaksi_id')
                ->join('produk', 'produk.id', '=', 'detil_transaksi_produk.produk_id')
                ->where('transaksi.status', 'Selesai')
                ->whereBetween('transaksi.created_at', [$start, $end])
                ->select('produk.nama_produk')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah) as qty_sold')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah * detil_transaksi_produk.harga_satuan) as revenue_est')
                ->groupBy('produk.nama_produk')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topProducts = $topProductsAgg->map(function ($row) {
                return (object) [
                    'nama_produk' => $row->nama_produk,
                    'qty_sold' => (int)$row->qty_sold,
                    'revenue_est' => (float)($row->revenue_est ?? 0),
                ];
            });

            $totalQtySold = (int)$topProductsAgg->sum('qty_sold');
            if ($topProducts->count() > 0) {
                $topProductName = $topProducts->first()->nama_produk;
            }

            $pieLabels = $topProducts->pluck('nama_produk')->all();
            $pieData = $topProducts->pluck('qty_sold')->map(fn($v) => (int)$v)->all();

            $totalTransaksiSelesai = Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $totalLayananSelesai = reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $omzetBersih = (float) Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga')
                + (float) reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('harga_total');

            $avgOrderValue = $totalTransaksiSelesai > 0
                ? (float) Transaksi::where('status', 'Selesai')
                    ->whereBetween('created_at', [$start, $end])
                    ->avg('total_harga')
                : 0.0;

            $statusKonversiCount = $totalTransaksiSelesai + $totalLayananSelesai;

            // Top layanan (pakai COUNT sebagai proxy kuantitas)
            $topLayananAgg = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('layanan_id')
                ->selectRaw('COUNT(*) as qty_sold')
                ->selectRaw('SUM(harga_total) as revenue_est')
                ->groupBy('layanan_id')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topLayanan = $topLayananAgg->map(fn($row) => (object) [
                'layanan_id' => $row->layanan_id,
                'qty_sold' => (int)($row->qty_sold ?? 0),
                'revenue_est' => (float)($row->revenue_est ?? 0),
            ]);

            if ($topLayanan->count() > 0) {
                $topLayananName = 'Layanan #' . $topLayanan->first()->layanan_id;
            }

            $topLayananPieLabels = $topLayanan->map(fn($r) => 'Layanan #' . $r->layanan_id)->all();
            $topLayananPieData = $topLayanan->pluck('qty_sold')->map(fn($v) => (int)$v)->all();

            $transactionsTable = collect()
                ->merge(
                    Transaksi::with('user')
                        ->where('status', 'Selesai')
                        ->whereBetween('created_at', [$start, $end])
                        ->orderByDesc('created_at')
                        ->limit(10)
                        ->get()
                        ->map(fn($t) => (object) [
                            'id' => $t->id,
                            'created_at' => $t->created_at,
                            'user' => $t->user,
                            'status' => $t->status,
                            'total' => $t->total_harga,
                            'tipe' => 'Produk',
                        ])
                )
                ->merge(
                    reservasi::with('user')
                        ->where('status', 'Selesai')
                        ->whereBetween('created_at', [$start, $end])
                        ->orderByDesc('created_at')
                        ->limit(10)
                        ->get()
                        ->map(fn($r) => (object) [
                            'id' => $r->id,
                            'created_at' => $r->created_at,
                            'user' => $r->user,
                            'status' => $r->status,
                            'total' => $r->harga_total,
                            'tipe' => 'Layanan',
                        ])
                )
                ->sortByDesc('created_at')
                ->take(12);
        }

        if ($mode === 'month') {
            $start = $now->copy()->subMonths($count - 1)->startOfMonth();
            $end = $now->copy()->endOfMonth();

            $produkMonthly = Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(total_harga) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym');

            $layananMonthly = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(harga_total) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym');

            for ($i = 0; $i < $count; $i++) {
                $ms = $start->copy()->addMonths($i);
                $ym = $ms->format('Y-m');

                $value = (float)($produkMonthly[$ym] ?? 0) + (float)($layananMonthly[$ym] ?? 0);

                $revenueLabels[] = $ms->format('M Y');
                $revenueData[] = $value;
                $totalRevenue += $value;
            }

            $topProductsAgg = DetilTransaksiProduk::query()
                ->join('transaksi', 'transaksi.id', '=', 'detil_transaksi_produk.transaksi_id')
                ->join('produk', 'produk.id', '=', 'detil_transaksi_produk.produk_id')
                ->where('transaksi.status', 'Selesai')
                ->whereBetween('transaksi.created_at', [$start, $end])
                ->select('produk.nama_produk')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah) as qty_sold')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah * detil_transaksi_produk.harga_satuan) as revenue_est')
                ->groupBy('produk.nama_produk')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topProducts = $topProductsAgg->map(function ($row) {
                return (object) [
                    'nama_produk' => $row->nama_produk,
                    'qty_sold' => (int)$row->qty_sold,
                    'revenue_est' => (float)($row->revenue_est ?? 0),
                ];
            });

            $totalQtySold = (int)$topProductsAgg->sum('qty_sold');
            if ($topProducts->count() > 0) {
                $topProductName = $topProducts->first()->nama_produk;
            }

            $pieLabels = $topProducts->pluck('nama_produk')->all();
            $pieData = $topProducts->pluck('qty_sold')->map(fn($v) => (int)$v)->all();

            $totalTransaksiSelesai = Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $totalLayananSelesai = reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $omzetBersih = (float) Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga')
                + (float) reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('harga_total');

            $avgOrderValue = $totalTransaksiSelesai > 0
                ? (float) Transaksi::where('status', 'Selesai')
                    ->whereBetween('created_at', [$start, $end])
                    ->avg('total_harga')
                : 0.0;

            $statusKonversiCount = $totalTransaksiSelesai + $totalLayananSelesai;

            $topLayananAgg = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('layanan_id')
                ->selectRaw('COUNT(*) as qty_sold')
                ->groupBy('layanan_id')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topLayananPieLabels = $topLayananAgg->map(fn($r) => 'Layanan #' . $r->layanan_id)->all();
            $topLayananPieData = $topLayananAgg->pluck('qty_sold')->map(fn($v) => (int)$v)->all();
            if ($topLayananAgg->count() > 0) {
                $topLayananName = 'Layanan #' . $topLayananAgg->first()->layanan_id;
            }

            $totalLayananSelesai = reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $totalTransaksiSelesai = Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $omzetBersih = (float) Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga')
                + (float) reservasi::where('status', 'Selesai')
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('harga_total');

            $ticketQty = $totalLayananSelesai;
            $statusKonversiCount = $totalTransaksiSelesai + $totalLayananSelesai;

            if ($totalTransaksiSelesai > 0) {
                $avgOrderValue = (float) Transaksi::where('status', 'Selesai')
                    ->whereBetween('created_at', [$start, $end])
                    ->avg('total_harga');
            }

            $transactionsTable = collect()
                ->merge(
                    Transaksi::with('user')
                        ->where('status', 'Selesai')
                        ->whereBetween('created_at', [$start, $end])
                        ->orderByDesc('created_at')
                        ->limit(10)
                        ->get()
                        ->map(fn($t) => (object) [
                            'id' => $t->id,
                            'created_at' => $t->created_at,
                            'user' => $t->user,
                            'status' => $t->status,
                            'total' => $t->total_harga,
                            'tipe' => 'Produk',
                        ])
                )
                ->merge(
                    reservasi::with('user')
                        ->where('status', 'Selesai')
                        ->whereBetween('created_at', [$start, $end])
                        ->orderByDesc('created_at')
                        ->limit(10)
                        ->get()
                        ->map(fn($r) => (object) [
                            'id' => $r->id,
                            'created_at' => $r->created_at,
                            'user' => $r->user,
                            'status' => $r->status,
                            'total' => $r->harga_total,
                            'tipe' => 'Layanan',
                        ])
                )
                ->sortByDesc('created_at')
                ->take(12);
        }

        // ---- YEAR ----
        if ($mode === 'year') {
            $start = $now->copy()->subYears($count - 1)->startOfYear();
            $end = $now->copy()->endOfYear();

            $produkYearly = Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEAR(created_at) as y, SUM(total_harga) as total')
                ->groupBy('y')
                ->pluck('total', 'y');

            $layananYearly = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEAR(created_at) as y, SUM(harga_total) as total')
                ->groupBy('y')
                ->pluck('total', 'y');

            for ($i = 0; $i < $count; $i++) {
                $y = (int)$start->copy()->addYears($i)->format('Y');
                $value = (float)($produkYearly[$y] ?? 0) + (float)($layananYearly[$y] ?? 0);
                $revenueLabels[] = (string)$y;
                $revenueData[] = $value;
                $totalRevenue += $value;
            }

            $topProductsAgg = DetilTransaksiProduk::query()
                ->join('transaksi', 'transaksi.id', '=', 'detil_transaksi_produk.transaksi_id')
                ->join('produk', 'produk.id', '=', 'detil_transaksi_produk.produk_id')
                ->where('transaksi.status', 'Selesai')
                ->whereBetween('transaksi.created_at', [$start, $end])
                ->select('produk.nama_produk')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah) as qty_sold')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah * detil_transaksi_produk.harga_satuan) as revenue_est')
                ->groupBy('produk.nama_produk')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topProducts = $topProductsAgg->map(function ($row) {
                return (object) [
                    'nama_produk' => $row->nama_produk,
                    'qty_sold' => (int)$row->qty_sold,
                    'revenue_est' => (float)($row->revenue_est ?? 0),
                ];
            });

            $totalQtySold = (int)$topProductsAgg->sum('qty_sold');
            if ($topProducts->count() > 0) {
                $topProductName = $topProducts->first()->nama_produk;
            }

            $pieLabels = $topProducts->pluck('nama_produk')->all();
            $pieData = $topProducts->pluck('qty_sold')->map(fn($v) => (int)$v)->all();

            $topLayananAgg = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('layanan_id')
                ->selectRaw('COUNT(*) as qty_sold')
                ->selectRaw('SUM(harga_total) as revenue_est')
                ->groupBy('layanan_id')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topLayanan = $topLayananAgg->map(fn($row) => (object) [
                'layanan_id' => $row->layanan_id,
                'qty_sold' => (int)($row->qty_sold ?? 0),
                'revenue_est' => (float)($row->revenue_est ?? 0),
            ]);

            $topLayananPieLabels = $topLayanan->map(fn($r) => 'Layanan #' . $r->layanan_id)->all();
            $topLayananPieData = $topLayanan->pluck('qty_sold')->map(fn($v) => (int)$v)->all();
            if ($topLayanan->count() > 0) {
                $topLayananName = 'Layanan #' . $topLayanan->first()->layanan_id;
            }

            $totalLayananSelesai = reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $totalTransaksiSelesai = Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $omzetBersih = (float) Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga')
                + (float) reservasi::where('status', 'Selesai')
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('harga_total');

            $ticketQty = $totalLayananSelesai;
            $statusKonversiCount = $totalTransaksiSelesai + $totalLayananSelesai;

            if ($totalTransaksiSelesai > 0) {
                $avgOrderValue = (float) Transaksi::where('status', 'Selesai')
                    ->whereBetween('created_at', [$start, $end])
                    ->avg('total_harga');
            }

            $transactionsTable = collect()
                ->merge(
                    Transaksi::with('user')
                        ->where('status', 'Selesai')
                        ->whereBetween('created_at', [$start, $end])
                        ->orderByDesc('created_at')
                        ->limit(10)
                        ->get()
                        ->map(fn($t) => (object) [
                            'id' => $t->id,
                            'created_at' => $t->created_at,
                            'user' => $t->user,
                            'status' => $t->status,
                            'total' => $t->total_harga,
                            'tipe' => 'Produk',
                        ])
                )
                ->merge(
                    reservasi::with('user')
                        ->where('status', 'Selesai')
                        ->whereBetween('created_at', [$start, $end])
                        ->orderByDesc('created_at')
                        ->limit(10)
                        ->get()
                        ->map(fn($r) => (object) [
                            'id' => $r->id,
                            'created_at' => $r->created_at,
                            'user' => $r->user,
                            'status' => $r->status,
                            'total' => $r->harga_total,
                            'tipe' => 'Layanan',
                        ])
                )
                ->sortByDesc('created_at')
                ->take(12);
        }

        $modeDisplay = [
            'day' => 'Harian',
            'month' => 'Bulanan',
            'year' => 'Tahunan',
        ][$mode] ?? $mode;

        return view('dashboard.owner', [
            'mode' => $mode,
            'count' => $count,
            'modeDisplay' => $modeDisplay,
            'omzetBersih' => $omzetBersih,
            'totalRevenue' => $totalRevenue,
            'totalTransaksiSelesai' => $totalTransaksiSelesai ?? 0,
            'totalLayananSelesai' => $totalLayananSelesai ?? 0,
            'totalQtySold' => $totalQtySold,
            'topProductName' => $topProductName,
            'topLayananName' => $topLayananName,
            'avgOrderValue' => $avgOrderValue,
            'statusKonversiCount' => $statusKonversiCount,
            'revenueLabels' => $revenueLabels,
            'revenueData' => $revenueData,
            'pieLabels' => $pieLabels,
            'pieData' => $pieData,
            'topProducts' => $topProducts,
            'topLayananPieLabels' => $topLayananPieLabels,
            'topLayananPieData' => $topLayananPieData,
            'topLayanan' => $topLayanan,
            'transactionsTable' => $transactionsTable,
            'petHotel' => \App\Models\hewan::count(),
        ]);
    }

    // Method untuk owner dashboard (sama dengan admin report)
    public function ownerLaporanPenjualan(Request $request)
    {
        return $this->adminLaporanPenjualan($request);
    }

    // Method untuk print laporan owner
    public function ownerLaporanPrint(Request $request)
    {
        $mode = $request->query('mode', 'day');
        $count = (int) $request->query('count', 30);

        if ($mode === 'month' && !in_array($count, [6, 12], true)) {
            $count = 12;
        }
        if ($mode === 'year' && !in_array($count, [5], true)) {
            $count = 5;
        }
        if ($mode === 'day' && !in_array($count, [7, 14, 30], true)) {
            $count = 30;
        }

        $now = now();
        $revenueLabels = [];
        $revenueData = [];
        $totalRevenue = 0.0;

        $topProductsAgg = collect();
        $topProducts = collect();
        $pieLabels = [];
        $pieData = [];
        $topProductName = null;

        $topLayanan = collect();
        $topLayananName = null;
        $topLayananPieLabels = [];
        $topLayananPieData = [];

        $totalQtySold = 0;
        $totalTransaksiSelesai = 0;
        $totalLayananSelesai = 0;
        $omzetBersih = 0.0;
        $pemasukkanProduk = 0;
        $pemasukkanLayanan = 0;

        if ($mode === 'day') {
            $start = $now->copy()->subDays($count - 1)->startOfDay();
            $end = $now->copy()->endOfDay();

            $produkDaily = Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as dt, SUM(total_harga) as total')
                ->groupBy('dt')
                ->pluck('total', 'dt');

            $layananDaily = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as dt, SUM(harga_total) as total')
                ->groupBy('dt')
                ->pluck('total', 'dt');

            for ($i = 0; $i < $count; $i++) {
                $dt = $start->copy()->addDays($i)->toDateString();
                $value = (float)($produkDaily[$dt] ?? 0) + (float)($layananDaily[$dt] ?? 0);
                $revenueLabels[] = $start->copy()->addDays($i)->format('d M');
                $revenueData[] = $value;
                $totalRevenue += $value;
            }

            $topProductsAgg = DetilTransaksiProduk::query()
                ->join('transaksi', 'transaksi.id', '=', 'detil_transaksi_produk.transaksi_id')
                ->join('produk', 'produk.id', '=', 'detil_transaksi_produk.produk_id')
                ->where('transaksi.status', 'Selesai')
                ->whereBetween('transaksi.created_at', [$start, $end])
                ->select('produk.nama_produk')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah) as qty_sold')
                ->selectRaw('SUM(detil_transaksi_produk.jumlah * detil_transaksi_produk.harga_satuan) as revenue_est')
                ->groupBy('produk.nama_produk')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topProducts = $topProductsAgg->map(fn($row) => (object) [
                'nama_produk' => $row->nama_produk,
                'qty_sold' => (int)$row->qty_sold,
                'revenue_est' => (float)($row->revenue_est ?? 0),
            ]);

            $totalQtySold = (int)$topProductsAgg->sum('qty_sold');
            if ($topProducts->count() > 0) {
                $topProductName = $topProducts->first()->nama_produk;
            }

            $pieLabels = $topProducts->pluck('nama_produk')->all();
            $pieData = $topProducts->pluck('qty_sold')->map(fn($v) => (int)$v)->all();

            $totalTransaksiSelesai = Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $totalLayananSelesai = reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $pemasukkanProduk = (float) Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga');

            $pemasukkanLayanan = (float) reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('harga_total');

            $omzetBersih = $pemasukkanProduk + $pemasukkanLayanan;

            $topLayananAgg = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->select('nama_layanan')
                ->selectRaw('COUNT(*) as qty_sold')
                ->selectRaw('SUM(harga_total) as revenue_est')
                ->groupBy('nama_layanan')
                ->orderByDesc('qty_sold')
                ->limit(10)
                ->get();

            $topLayanan = $topLayananAgg->map(fn($row) => (object) [
                'nama_layanan' => $row->nama_layanan,
                'qty_sold' => (int)($row->qty_sold ?? 0),
                'revenue_est' => (float)($row->revenue_est ?? 0),
            ]);

            if ($topLayanan->count() > 0) {
                $topLayananName = $topLayanan->first()->nama_layanan;
            }

            $topLayananPieLabels = $topLayanan->pluck('nama_layanan')->all();
            $topLayananPieData = $topLayanan->pluck('qty_sold')->map(fn($v) => (int)$v)->all();
        } elseif ($mode === 'month') {
            $start = $now->copy()->subMonths($count - 1)->startOfMonth();
            $end = $now->copy()->endOfMonth();

            $produkMonthly = Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(total_harga) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym');

            $layananMonthly = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(harga_total) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym');

            for ($i = 0; $i < $count; $i++) {
                $ms = $start->copy()->addMonths($i);
                $ym = $ms->format('Y-m');
                $value = (float)($produkMonthly[$ym] ?? 0) + (float)($layananMonthly[$ym] ?? 0);
                $revenueLabels[] = $ms->format('M Y');
                $revenueData[] = $value;
                $totalRevenue += $value;
            }

            $totalTransaksiSelesai = Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $totalLayananSelesai = reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $pemasukkanProduk = (float) Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga');

            $pemasukkanLayanan = (float) reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('harga_total');

            $omzetBersih = $pemasukkanProduk + $pemasukkanLayanan;
        } else {
            $start = $now->copy()->subYears($count - 1)->startOfYear();
            $end = $now->copy()->endOfYear();

            $produkYearly = Transaksi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEAR(created_at) as y, SUM(total_harga) as total')
                ->groupBy('y')
                ->pluck('total', 'y');

            $layananYearly = reservasi::query()
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEAR(created_at) as y, SUM(harga_total) as total')
                ->groupBy('y')
                ->pluck('total', 'y');

            for ($i = 0; $i < $count; $i++) {
                $y = (int)$start->copy()->addYears($i)->format('Y');
                $value = (float)($produkYearly[$y] ?? 0) + (float)($layananYearly[$y] ?? 0);
                $revenueLabels[] = (string)$y;
                $revenueData[] = $value;
                $totalRevenue += $value;
            }

            $totalTransaksiSelesai = Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $totalLayananSelesai = reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $pemasukkanProduk = (float) Transaksi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga');

            $pemasukkanLayanan = (float) reservasi::where('status', 'Selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('harga_total');

            $omzetBersih = $pemasukkanProduk + $pemasukkanLayanan;
        }

        $modeDisplay = match($mode) {
            'month' => 'Bulanan',
            'year' => 'Tahunan',
            default => 'Harian'
        };

        $printDate = now()->format('d F Y H:i');

        return view('dashboard.owner.laporan-print', [
            'mode' => $mode,
            'modeDisplay' => $modeDisplay,
            'count' => $count,
            'totalRevenue' => $totalRevenue,
            'pemasukkanProduk' => $pemasukkanProduk,
            'pemasukkanLayanan' => $pemasukkanLayanan,
            'omzetBersih' => $omzetBersih,
            'totalQtySold' => $totalQtySold,
            'totalTransaksiSelesai' => $totalTransaksiSelesai,
            'totalLayananSelesai' => $totalLayananSelesai,
            'topProducts' => $topProducts,
            'topLayanan' => $topLayanan,
            'revenueLabels' => $revenueLabels,
            'revenueData' => $revenueData,
            'pieLabels' => $pieLabels,
            'pieData' => $pieData,
            'topLayananPieLabels' => $topLayananPieLabels,
            'topLayananPieData' => $topLayananPieData,
            'printDate' => $printDate,
        ]);
    }

}



