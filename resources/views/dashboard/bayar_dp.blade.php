@extends('layouts.app')

@section('title', 'Pembayaran DP Reservasi')

@section('content')
<div class="dp-container">
    <h2 class="dp-title">📸 Scan QRIS untuk Bayar DP</h2>

    <div class="dp-info-box">
        <h3>Rincian Reservasi</h3>
        <p><strong>Layanan:</strong> {{ $reservasi->nama_layanan }}</p>
        <p><strong>Harga Total:</strong> Rp {{ number_format($reservasi->harga_total, 0, ',', '.') }}</p>
        <hr class="dp-hr">

        <p style="font-size: 18px; color: var(--purple-800); font-weight: bold; margin: 0;">
            Wajib Bayar DP: Rp {{ number_format($reservasi->harga_total * 0.20, 0, ',', '.') }}
        </p>
    </div>

    <div class="dp-qris-area">
       <img src="{{ asset('storage/QRIS.png') }}" alt="QRIS Paw Center" style="width: 250px; height: auto; margin-bottom: 15px; border: 1px solid #eee;">
        <h4 style="font-size: 18px; color: var(--purple-900); font-weight: bold; margin: 0;">PAW CENTER (D&F PETS)</h4>
        <p style="color: #666; font-size: 12px;">NMID: ID1234567890123</p>
    </div>

    <form action="{{ route('reservasi.upload', $reservasi->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom: 20px;">
            <label class="dp-form-label">Sudah bayar? Upload Screenshot Berhasilnya di Sini:</label>
            <input type="file" name="bukti_pembayaran_dp" accept="image/jpeg, image/png, image/jpg" required class="dp-input-file">
            <small class="dp-error-small">
                <strong>Penting:</strong> Reservasi hanya akan dikonfirmasi jika bukti transfer valid.
            </small>
        </div>

        <button type="submit" class="dp-btn-submit">Konfirmasi & Upload Bukti DP</button>
    </form>
</div>
@endsection
