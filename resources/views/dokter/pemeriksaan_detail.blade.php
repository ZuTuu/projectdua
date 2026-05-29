@extends('layouts.app')

@section('title', 'Detail Pemeriksaan - Paw Center')

@section('content')
<style>
    .card-dokter { background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #E6E6FA; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    .label { color:#666; font-size:13px; margin-top:10px; margin-bottom: 6px; display: block; font-weight: 500;}
    .value { color:#222; font-size:15px; font-weight:600; }
    .btn { display: inline-block; padding: 12px 14px; border-radius: 8px; border: none; cursor: pointer; text-decoration: none; }
    .btn-primary { background: #800080; color:#fff; }
    .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
    .form-control:focus { outline: none; border-color: #800080; }
    textarea.form-control { resize: vertical; }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
</style>

<div class="container">
    <h2 style="color:#333;margin-bottom:20px;">🧾 Detail Pemeriksaan</h2>

    <div class="card-dokter" style="margin-bottom:25px;">
        <div class="grid">
            <div>
                <div class="label">Waktu Reservasi</div>
                <div class="value">{{ optional($pemeriksaan->created_at)->format('d M Y, H:i') }}</div>
            </div>
            <div>
                <div class="label">Status</div>
                <div class="value" style="color: #0284c7;">{{ $pemeriksaan->status }}</div>
            </div>
            <div>
                <div class="label">Nama Hewan</div>
                <div class="value">🐾 {{ $pemeriksaan->hewan->nama_hewan ?? $pemeriksaan->pet_name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="label">Jenis Hewan</div>
                <div class="value">{{ $pemeriksaan->hewan->jenis_hewan ?? 'N/A' }} ({{ $pemeriksaan->hewan->jenis_kelamin ?? '-' }})</div>
            </div>
            <div>
                <div class="label">Pemilik</div>
                <div class="value">👤 {{ $pemeriksaan->user->name ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <div class="card-dokter">
        <h3 style="color:#800080;margin-top:0;margin-bottom:20px;">✍️ Catat Rekam Medis</h3>

        <form action="{{ route('dokter.simpanRM') }}" method="POST">
            @csrf
            <input type="hidden" name="hewan_id" value="{{ $pemeriksaan->hewan_id }}">
            <input type="hidden" name="reservasi_id" value="{{ $pemeriksaan->id }}">

            @if($errors->any())
                <div style="margin-bottom:15px; padding: 12px; background: #FFEBEE; border: 1px solid #F44336; color: #C62828; border-radius: 10px;">
                    <strong>Ups, ada yang kurang:</strong>
                    <ul style="margin: 8px 0 0 18px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div style="margin-bottom:15px; padding: 12px; background: #dcfce7; border: 1px solid #22c55e; color: #15803d; border-radius: 10px;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div style="margin-bottom:20px;">
                <label class="label">Diagnosa Penyakit / Kondisi <span style="color:red;">*</span></label>
                <input type="text" name="diagnosa" class="form-control" value="{{ old('diagnosa') }}" required placeholder="Contoh: Flu kucing, jamur, atau sehat lahir batin...">
            </div>

            <div style="margin-bottom:20px;">
                <label class="label">Tindakan / Terapi <span style="color:red;">*</span></label>
                <select name="tindakan" class="form-control" required>
                    <option value="" disabled {{ old('tindakan') ? '' : 'selected' }}>-- Pilih Tindakan yang Dilakukan --</option>
                    <option value="Pemeriksaan Rutin" {{ old('tindakan') == 'Pemeriksaan Rutin' ? 'selected' : '' }}>Pemeriksaan Rutin</option>
                    <option value="Vaksinasi" {{ old('tindakan') == 'Vaksinasi' ? 'selected' : '' }}>Vaksinasi</option>
                    <option value="Sterilisasi" {{ old('tindakan') == 'Sterilisasi' ? 'selected' : '' }}>Sterilisasi</option>
                    <option value="Rawat Inap" {{ old('tindakan') == 'Rawat Inap' ? 'selected' : '' }}>Rawat Inap</option>
                    <option value="Pemberian Obat" {{ old('tindakan') == 'Pemberian Obat' ? 'selected' : '' }}>Pemberian Obat / Injeksi</option>
                    <option value="Operasi" {{ old('tindakan') == 'Operasi' ? 'selected' : '' }}>Operasi</option>
                    <option value="Lainnya" {{ old('tindakan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div style="margin-bottom:20px;">
                <label class="label">Catatan untuk Pelanggan (Opsional)</label>
                <textarea name="catatan" class="form-control" rows="4" placeholder="Tulis resep obat, anjuran makan, atau pesan untuk pemilik anabul di sini...">{{ old('catatan') }}</textarea>
                <small style="color:#888; margin-top:5px; display:block;">*Catatan ini bakal muncul di dashboard rekam medis milik pelanggan.</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; display:block; text-align:center; font-size: 15px; font-weight: bold; margin-top: 10px;">
                Simpan & Selesaikan Rekam Medis
            </button>
        </form>
    </div>
</div>
@endsection
