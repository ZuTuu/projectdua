@extends('layouts.app')

@section('title', 'Rekam Medis Anabul')

@section('content')
<div class="content">
    <div style="margin-bottom: 25px;">
        <h2 style="margin:0; color: #1e1b4b;">🩺 Rekam Medis Anabul</h2>
        <p style="margin:5px 0 0 0; color: #64748b;">Pantau keluhan dan hasil pemeriksaan dokter untuk peliharaan kesayanganmu.</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
        @forelse($riwayatMedis as $rm)
            @php
                $catatanDokter = \App\Models\RekamMedis::where('reservasi_id', $rm->id)->first();
            @endphp
            <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">

                <div style="display: flex; justify-content: space-between; border-bottom: 2px dashed #edf2f7; padding-bottom: 15px; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin: 0; color: #4c1d95; font-size: 18px;">🐱 {{ $rm->pet_name ?? 'Anabul' }} <span style="font-size: 14px; color: #64748b; font-weight: normal;">(#RES-{{ $rm->id }})</span></h3>
                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #64748b;"><i class="fas fa-stethoscope"></i> {{ $rm->nama_layanan }} | 📅 {{ \Carbon\Carbon::parse($rm->tanggal)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <span style="padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; display: inline-block;
                            {{ $rm->status == 'Selesai' ? 'background: #dcfce7; color: #16a34a;' : 'background: #e0f2fe; color: #0284c7;' }}">
                            {{ $rm->status }}
                        </span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">

                    <div style="background: #fffbeb; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                        <h4 style="margin: 0 0 10px 0; color: #b45309; font-size: 14px;"><i class="fas fa-comment-medical"></i> Keluhan / Gejala Awal</h4>
                        <p style="margin: 0; font-size: 14px; color: #555; white-space: pre-wrap;">{{ $rm->keluhan ?? 'Tidak ada catatan keluhan yang dimasukkan saat pendaftaran.' }}</p>
                    </div>

                    <div style="background: #f0fdf4; padding: 15px; border-radius: 8px; border-left: 4px solid #22c55e;">
                        <h4 style="margin: 0 0 10px 0; color: #15803d; font-size: 14px;"><i class="fas fa-user-md"></i> Hasil Pemeriksaan Dokter</h4>

                        @if($catatanDokter)
                            <div style="font-size: 13px; color: #334155;">
                                <p style="margin: 0 0 8px 0;"><strong>Diagnosa:</strong><br> {{ $catatanDokter->diagnosa ?? '-' }}</p>
                                <p style="margin: 0 0 8px 0;"><strong>Tindakan:</strong><br> {{ $catatanDokter->tindakan ?? '-' }}</p>
                                <p style="margin: 0;"><strong>Catatan:</strong><br> {{ $catatanDokter->catatan ?? '-' }}</p>
                            </div>
                            <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6; margin-top: 20px;">
                                <h4 style="margin: 0 0 10px 0; color: #1d4ed8; font-size: 14px;"><i class="fas fa-file-invoice-dollar"></i> Ringkasan Biaya Kunjungan</h4>
                                <div style="display: flex; gap: 25px; font-size: 13px; color: #334155; flex-wrap: wrap;">
                                    <div>
                                        <span style="color: #64748b;">Harga Layanan Dasar:</span><br>
                                        <strong>Rp {{ number_format($rm->harga_total ?? 0, 0, ',', '.') }}</strong>
                                    </div>
                                    <div>
                                        <span style="color: #64748b;">Biaya Tambahan (Obat/Tindakan):</span><br>
                                        <strong style="color: #b45309;">Rp {{ number_format($catatanDokter->biaya_tambahan ?? 0, 0, ',', '.') }}</strong>
                                    </div>
                                    <div>
                                        <span style="color: #64748b;">Total Biaya Akhir (Belum Potong DP):</span><br>
                                        <strong style="color: #0f766e; font-size: 14px;">Rp {{ number_format(($rm->harga_total ?? 0) + ($catatanDokter->biaya_tambahan ?? 0), 0, ',', '.') }}</strong>
                                    </div>
                                    <div style="width: 100%; border-top: 1px solid #cbd5e1; margin-top: 5px; padding-top: 10px;">
                                        <span style="color: #64748b;">Sisa Wajib Bayar (Setelah Potong DP):</span><br>
                                        <strong style="color: #e11d48; font-size: 15px;">Rp {{ number_format(($rm->harga_total ?? 0) + ($catatanDokter->biaya_tambahan ?? 0) - ($rm->dp ?? 0), 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>

                        </div> {{-- Penutup background white (Card Utama) --}}
                        @elseif($rm->status == 'Selesai')
                            <p style="margin: 0; font-size: 14px; color: #64748b; font-style: italic;">Data rekam medis sedang diproses untuk diarsipkan.</p>
                        @else
                            <p style="margin: 0; font-size: 14px; color: #64748b; font-style: italic;">Sedang menunggu pemeriksaan dokter... 🩺</p>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 50px; background: white; border-radius: 12px; border: 2px dashed #e2e8f0;">
                <div style="font-size: 40px; margin-bottom: 15px;">📂</div>
                <h4 style="color: #475569; margin: 0;">Belum Ada Rekam Medis</h4>
                <p style="color: #94a3b8; font-size: 14px; margin-top: 5px;">Rekam medis akan muncul di sini setelah anabul kamu diperiksa dokter.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6 px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-100">
        {{ $riwayatMedis->links() }}
    </div>
</div>
@endsection
