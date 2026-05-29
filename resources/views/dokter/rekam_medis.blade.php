@extends('layouts.app')

@section('title', 'Rekam Medis Dokter - Paw Center')

@section('content')
<style>
    .card-dokter { background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #E6E6FA; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    .table-toolbar { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 16px; }
    .field { display:flex; flex-direction:column; gap:6px; }
    .label-soft { color:#6b7280; font-size:12px; font-weight: bold; }
    input[type="text"], select {
        padding: 10px 12px; border: 1px solid #ddd; border-radius: 10px; min-width: 220px; font-size: 14px;
    }
    input[type="text"]:focus, select:focus { outline: none; border-color: #800080; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid #eee; }
    th { color: #888; font-size: 13px; text-transform: uppercase; background: #faf5ff; }
    .btn-primary { display: inline-block; padding: 8px 14px; border-radius: 8px; background: #800080; color: white; text-decoration: none; border:none; cursor:pointer; font-size: 13px; font-weight: bold; }
    .btn-primary:hover { background: #610061; }
    .btn-secondary { display: inline-block; padding: 8px 14px; border-radius: 8px; background: #f1f5f9; color: #334155; text-decoration: none; border:1px solid #cbd5e1; cursor:pointer; font-size: 13px; font-weight: bold; }
    .btn-secondary:hover { background: #e2e8f0; }
    .no-data { text-align:center; color:#999; padding: 30px !important; }
    @media (max-width: 768px) {
        input[type="text"], select { min-width: 100%; }
        .table-responsive { overflow-x: auto; }
    }
</style>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 25px;">
        <div>
            <h2 style="color: #36005E; margin-bottom: 8px; margin-top: 0;">🗂️ Riwayat Rekam Medis</h2>
            <p style="color: #64748b; margin: 0; font-size: 14px;">Pantau histori pemeriksaan pasien. Gunakan filter untuk pencarian cepat.</p>
        </div>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 20px; padding: 16px; border-radius: 12px; background: #dcfce7; border: 1px solid #22c55e; color: #15803d; font-weight: 500;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="card-dokter">
        <div class="table-toolbar">
            <div class="field">
                <div class="label-soft">Pencarian</div>
                <input id="rm-search" type="text" placeholder="Cari hewan, pemilik, atau diagnosa..." value="{{ request('q') }}" />
            </div>

            <div class="field">
                <div class="label-soft">Filter Dokter</div>
                <select id="rm-dokter" name="dokter">
                    <option value="all">Semua Dokter</option>
                    @php
                        $selectedDokterId = request('dokter');
                        $dokterList = $rekamMedis->pluck('user')->unique('id')->values()->all();
                    @endphp
                    @foreach($dokterList as $dokter)
                        @if(isset($dokter->id))
                            <option value="{{ $dokter->id }}" {{ (string)$selectedDokterId === (string)$dokter->id ? 'selected' : '' }}>
                                drh. {{ $dokter->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="field">
                <div class="label-soft">Filter Tahun</div>
                <select id="rm-tahun" name="tahun">
                    <option value="all">Semua Tahun</option>
                    @php
                        $years = $rekamMedis->map(fn($rm) => optional($rm->created_at)->format('Y'))->filter()->unique()->sort()->values();
                        $selectedYear = request('tahun');
                    @endphp
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ (string)$selectedYear === (string)$y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field" style="justify-content:flex-end;">
                <button id="rm-clear" class="btn-secondary" type="button" style="padding: 10px 16px;">🔄 Reset Filter</button>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pasien (Hewan)</th>
                        <th>Pemilik</th>
                        <th>Dokter</th>
                        <th>Diagnosa Utama</th>
                        <th>Tindakan</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="rm-tbody">
                    @forelse($rekamMedis as $rm)
                        @php
                            $dokterId = optional($rm->user)->id;
                            $tahun = optional($rm->created_at)->format('Y');

                            // 🔥 Tambahin fallback ke tabel reservasi di sini biar anti-N/A!
                            $hewanNama = $rm->hewan->nama_hewan ?? $rm->reservasi->pet_name ?? '';
                            $pemilikNama = optional(optional($rm->hewan)->user)->name ?? optional(optional($rm->reservasi)->user)->name ?? '';
                            $jenisHewan = $rm->hewan->jenis_hewan ?? '-';

                            $dokterNama = $rm->nama_dokter ?? $rm->user->name ?? '';
                            $diagnosa = $rm->diagnosa ?? '';
                            $tindakan = $rm->tindakan ?? '';
                        @endphp
                        <tr class="rm-row"
                            data-q="{{ strtolower($hewanNama.' '.$pemilikNama.' '.$diagnosa) }}"
                            data-dokter-id="{{ $dokterId }}"
                            data-tahun="{{ $tahun }}"
                        >
                            <td style="font-weight: 500;">{{ optional($rm->created_at)->format('d M Y') }}</td>
                            <td>
                                <strong>{{ $hewanNama ?: 'N/A' }}</strong><br>
                                <small style="color: #888;">{{ $jenisHewan }}</small>
                            </td>
                            <td>{{ $pemilikNama ?: 'N/A' }}</td>
                            <td>{{ $dokterNama ?: 'N/A' }}</td>
                            <td><span style="background: #fef2f2; color: #991b1b; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold;">{{ \Illuminate\Support\Str::limit($diagnosa, 30) }}</span></td>
                            <td>{{ \Illuminate\Support\Str::limit($tindakan, 30) }}</td>
                            <td style="white-space:nowrap; text-align: center;">
                                <a class="btn-primary" href="{{ route('dokter.rekam-medis.detail', ['id' => $rm->id]) }}">🔍 Detail</a>
                                <a class="btn-secondary" style="margin-left:5px;" href="{{ route('dokter.rekam-medis.pdf', ['id' => $rm->id]) }}" target="_blank">🖨️ PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="no-data">
                                <div style="font-size: 40px; margin-bottom: 10px;">📂</div>
                                Belum ada rekam medis yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    (function(){
        const searchEl = document.getElementById('rm-search');
        const dokterEl = document.getElementById('rm-dokter');
        const tahunEl = document.getElementById('rm-tahun');
        const clearBtn = document.getElementById('rm-clear');
        const rows = Array.from(document.querySelectorAll('.rm-row'));

        function applyFilters(){
            const q = (searchEl?.value || '').trim().toLowerCase();
            const dokterId = dokterEl?.value || 'all';
            const tahun = tahunEl?.value || 'all';

            rows.forEach(row => {
                const rowQ = (row.dataset.q || '');
                const rowDokter = row.dataset.dokterId || '';
                const rowTahun = row.dataset.tahun || '';

                const matchQ = !q || rowQ.includes(q);
                const matchDokter = dokterId === 'all' || String(rowDokter) === String(dokterId);
                const matchTahun = tahun === 'all' || String(rowTahun) === String(tahun);

                row.style.display = (matchQ && matchDokter && matchTahun) ? '' : 'none';
            });
        }

        if (searchEl) searchEl.addEventListener('input', applyFilters);
        if (dokterEl) dokterEl.addEventListener('change', applyFilters);
        if (tahunEl) tahunEl.addEventListener('change', applyFilters);

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                if (searchEl) searchEl.value = '';
                if (dokterEl) dokterEl.value = 'all';
                if (tahunEl) tahunEl.value = 'all';
                applyFilters();
            });
        }

        applyFilters();
    })();
</script>
@endsection
