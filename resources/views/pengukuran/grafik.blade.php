@extends('layouts.app')

@section('title', 'Grafik Pertumbuhan - ' . $anak->nama_anak)

@section('styles')
<style>
    body { background: linear-gradient(135deg,#E8F4FF 0%,#D4E9FF 50%,#C5E2FF 100%) !important; }
    .card { background:#fff; border-radius:16px; padding:24px; box-shadow:0 6px 24px rgba(16,24,40,0.06); margin-bottom:24px; }
    .page-title { font-size:24px; font-weight:700; color:#1E3A5F; margin-bottom:4px; }
    .page-subtitle { font-size:14px; color:#64748B; margin-bottom:24px; }
    .info-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
    @media(max-width:768px){ .info-grid{ grid-template-columns:repeat(2,1fr); } }
    .info-card { background:linear-gradient(135deg,#EFF6FF,#DBEAFE); border-radius:12px; padding:16px; text-align:center; }
    .info-card .label { font-size:11px; font-weight:600; color:#64748B; text-transform:uppercase; letter-spacing:0.5px; }
    .info-card .value { font-size:22px; font-weight:700; color:#1E3A5F; margin-top:4px; }
    .chart-tabs { display:flex; gap:8px; margin-bottom:20px; }
    .chart-tab { padding:8px 16px; border:2px solid #E5E7EB; background:#fff; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; color:#64748B; transition:all 0.2s; }
    .chart-tab.active { border-color:#246BCE; background:#EFF6FF; color:#246BCE; }
    .form-row { display:grid; grid-template-columns:repeat(3,1fr) auto; gap:12px; align-items:end; }
    @media(max-width:768px){ .form-row{ grid-template-columns:1fr; } }
    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-group label { font-size:13px; font-weight:600; color:#374151; }
    .form-group input, .form-group select { padding:10px 14px; border:2px solid #E5E7EB; border-radius:8px; font-size:14px; }
    .form-group input:focus, .form-group select:focus { outline:none; border-color:#246BCE; }
    .btn { padding:10px 20px; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:6px; }
    .btn-primary { background:#246BCE; color:#fff; } .btn-primary:hover { background:#1D58A8; }
    .riwayat-table { width:100%; border-collapse:collapse; font-size:13px; }
    .riwayat-table th { background:#F8FAFC; padding:10px 12px; text-align:left; font-weight:600; color:#4A6FA3; border-bottom:2px solid #E5E7EB; }
    .riwayat-table td { padding:10px 12px; border-bottom:1px solid #F1F5F9; }
    .badge { display:inline-block; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:600; }
    .badge-success { background:#D1FAE5; color:#065F46; }
    .badge-warning { background:#FEF3C7; color:#92400E; }
    .badge-danger  { background:#FEE2E2; color:#991B1B; }
    .badge-info    { background:#DBEAFE; color:#1E40AF; }
    .badge-secondary { background:#F3F4F6; color:#374151; }
    #toast { position:fixed; top:20px; right:20px; z-index:9999; }
    .toast-item { padding:12px 20px; border-radius:10px; color:#fff; font-size:14px; font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,0.15); margin-bottom:8px; }
    .toast-success { background:#10B981; } .toast-error { background:#EF4444; }
</style>
@endsection

@section('content')
<div id="toast"></div>

<div style="margin-bottom:16px;">
    <a href="{{ route('anak.index') }}" style="color:#246BCE;text-decoration:none;font-size:14px;"><i class="fas fa-arrow-left"></i> Kembali ke Data Anak</a>
</div>

<h1 class="page-title"><i class="fas fa-chart-line" style="color:#246BCE;"></i> Grafik Pertumbuhan</h1>
<p class="page-subtitle">{{ $anak->nama_anak }} — {{ $anak->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>

{{-- Info Cards --}}
<div class="info-grid">
    <div class="info-card">
        <div class="label">Nama Anak</div>
        <div class="value" style="font-size:16px;">{{ $anak->nama_anak }}</div>
    </div>
    <div class="info-card">
        <div class="label">Tanggal Lahir</div>
        <div class="value" style="font-size:16px;">{{ \Carbon\Carbon::parse($anak->tanggal_lahir)->format('d/m/Y') }}</div>
    </div>
    <div class="info-card">
        <div class="label">Usia Sekarang</div>
        <div class="value">{{ $anak->umur_display }}</div>
    </div>
    <div class="info-card">
        <div class="label">Status Gizi</div>
        <div class="value" style="font-size:15px;">{{ $anak->status_gizi ?? 'Belum diukur' }}</div>
    </div>
</div>

{{-- Form Input Pengukuran --}}
<div class="card">
    <h3 style="font-size:16px;font-weight:700;color:#1E3A5F;margin-bottom:16px;"><i class="fas fa-plus-circle" style="color:#246BCE;"></i> Input Pengukuran Baru</h3>
    <form id="formPengukuran" onsubmit="submitPengukuran(event)">
        <input type="hidden" name="anak_id" value="{{ $anak->id }}">
        <div class="form-row">
            <div class="form-group">
                <label>Tanggal Ukur <span style="color:red">*</span></label>
                <input type="date" name="tanggal_ukur" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="form-group">
                <label>Berat Badan (kg) <span style="color:red">*</span></label>
                <input type="number" name="bb_kg" placeholder="Contoh: 7.5" step="0.1" min="2" max="25" required>
            </div>
            <div class="form-group">
                <label>Tinggi Badan (cm) <span style="color:red">*</span></label>
                <input type="number" name="tb_cm" placeholder="Contoh: 65" step="0.1" min="45" max="120" required>
            </div>
        </div>
        <div class="form-row" style="margin-top:12px;">
            <div class="form-group">
                <label>Lingkar Kepala (cm)</label>
                <input type="number" name="lk_cm" placeholder="Opsional" step="0.1" min="30" max="55">
            </div>
            <div class="form-group">
                <label>Cara Ukur <span style="color:red">*</span></label>
                <select name="cara_ukur" required>
                    <option value="berbaring">Berbaring (< 2 tahun)</option>
                    <option value="berdiri">Berdiri (≥ 2 tahun)</option>
                </select>
            </div>
            <div></div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary" id="btnSimpan"><i class="fas fa-save"></i> Simpan Pengukuran</button>
            </div>
        </div>
    </form>
</div>

{{-- Grafik --}}
<div class="card">
    <div class="chart-tabs">
        <button class="chart-tab active" onclick="switchChart('bbu')">BB/U</button>
        <button class="chart-tab" onclick="switchChart('tbu')">TB/U</button>
        <button class="chart-tab" onclick="switchChart('bbtb')">BB/TB</button>
    </div>
    <canvas id="growthChart" style="max-height:400px;"></canvas>
</div>

{{-- Riwayat Pengukuran --}}
<div class="card">
    <h3 style="font-size:16px;font-weight:700;color:#1E3A5F;margin-bottom:16px;"><i class="fas fa-history" style="color:#246BCE;"></i> Riwayat Pengukuran</h3>
    <div style="overflow-x:auto;">
        <table class="riwayat-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Usia (bln)</th>
                    <th>BB (kg)</th>
                    <th>TB (cm)</th>
                    <th>LK (cm)</th>
                    <th>Z-BBU</th>
                    <th>Z-TBU</th>
                    <th>Z-BBTB</th>
                    <th>Status Gizi</th>
                </tr>
            </thead>
            <tbody id="riwayatBody">
                @forelse($riwayat as $r)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($r->tanggal_ukur)->format('d/m/Y') }}</td>
                    <td>{{ number_format($r->umur_bulan, 1) }}</td>
                    <td>{{ $r->bb_kg }}</td>
                    <td>{{ $r->tb_pb_cm }}</td>
                    <td>{{ $r->lk_cm ?? '-' }}</td>
                    <td>{{ $r->z_bbu ? number_format($r->z_bbu, 2) : '-' }}</td>
                    <td>{{ $r->z_tbu ? number_format($r->z_tbu, 2) : '-' }}</td>
                    <td>{{ $r->z_bbtb ? number_format($r->z_bbtb, 2) : '-' }}</td>
                    <td>
                        @php
                            $s = $r->overall_8 ?? 'Belum diukur';
                            $cls = match(true) {
                                $s === 'Gizi Baik' => 'success',
                                str_contains($s, 'Stunting') || str_contains($s, 'Kurang') => 'warning',
                                str_contains($s, 'Lebih') || $s === 'Obesitas' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge badge-{{ $cls }}">{{ $s }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:30px;color:#9CA3AF;">Belum ada riwayat pengukuran</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const riwayatData = @json($riwayat);
let growthChart = null;
let currentChartType = 'bbu';

function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast-item toast-${type}`;
    el.innerHTML = `<i class="fas fa-${type==='success'?'check-circle':'times-circle'}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

function switchChart(type) {
    currentChartType = type;
    document.querySelectorAll('.chart-tab').forEach((b,i) => b.classList.toggle('active', ['bbu','tbu','bbtb'][i] === type));
    renderChart(type);
}

function renderChart(type) {
    const ctx = document.getElementById('growthChart');
    if (growthChart) growthChart.destroy();

    const labels   = riwayatData.map(r => parseFloat(r.umur_bulan).toFixed(1) + ' bln');
    const zMap     = { bbu: 'z_bbu', tbu: 'z_tbu', bbtb: 'z_bbtb' };
    const yMap     = { bbu: 'bb_kg', tbu: 'tb_pb_cm', bbtb: 'bb_kg' };
    const titleMap = {
        bbu:  'BB/U — Berat Badan menurut Umur',
        tbu:  'TB/U — Tinggi Badan menurut Umur',
        bbtb: 'BB/TB — Berat Badan menurut Tinggi Badan'
    };
    const yLabel = { bbu: 'Berat Badan (kg)', tbu: 'Tinggi Badan (cm)', bbtb: 'Berat Badan (kg)' };

    const values  = riwayatData.map(r => parseFloat(r[yMap[type]] || 0));
    const zscores = riwayatData.map(r => r[zMap[type]] ? parseFloat(r[zMap[type]]) : null);

    // ── Garis referensi WHO (-3SD s/d +3SD) ──────────────────────────
    // Nilai z-score konstan untuk setiap garis referensi
    const whoLines = [
        { label: '-3 SD (Sangat Kurang)', z: -3, color: '#DC2626', dash: [6,3] },
        { label: '-2 SD (Kurang)',         z: -2, color: '#F59E0B', dash: [4,3] },
        { label: '-1 SD',                  z: -1, color: '#FCD34D', dash: [3,3] },
        { label: 'Median (Normal)',         z:  0, color: '#10B981', dash: []    },
        { label: '+1 SD',                  z:  1, color: '#FCD34D', dash: [3,3] },
        { label: '+2 SD (Lebih)',           z:  2, color: '#F59E0B', dash: [4,3] },
        { label: '+3 SD (Obesitas)',        z:  3, color: '#DC2626', dash: [6,3] },
    ];

    // Buat dataset garis WHO (nilai z konstan sepanjang sumbu x)
    const whoDatasets = whoLines.map(line => ({
        label:           line.label,
        data:            labels.map(() => line.z),
        borderColor:     line.color,
        backgroundColor: 'transparent',
        borderWidth:     1.5,
        borderDash:      line.dash,
        pointRadius:     0,
        tension:         0,
        yAxisID:         'y1',
    }));

    growthChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                // Dataset utama: nilai aktual anak
                {
                    label:            yLabel[type],
                    data:             values,
                    borderColor:      '#246BCE',
                    backgroundColor:  'rgba(36,107,206,0.08)',
                    borderWidth:      2.5,
                    pointRadius:      5,
                    pointBackgroundColor: '#246BCE',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    tension:          0.3,
                    fill:             true,
                    yAxisID:          'y',
                    order:            0,
                },
                // Dataset z-score anak
                {
                    label:            'Z-Score Anak',
                    data:             zscores,
                    borderColor:      '#7C3AED',
                    backgroundColor:  'rgba(124,58,237,0.08)',
                    borderWidth:      2,
                    pointRadius:      4,
                    pointBackgroundColor: '#7C3AED',
                    borderDash:       [5,3],
                    tension:          0.3,
                    yAxisID:          'y1',
                    order:            1,
                },
                // Garis referensi WHO
                ...whoDatasets.map((d, i) => ({ ...d, order: i + 2 })),
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                title: {
                    display: true,
                    text:    titleMap[type],
                    font:    { size: 14, weight: 'bold' },
                    color:   '#1E3A5F',
                    padding: { bottom: 16 },
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        filter: item => item.text !== 'Z-Score Anak' || zscores.some(z => z !== null),
                        font:   { size: 11 },
                        boxWidth: 20,
                        padding: 12,
                    }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            if (ctx.datasetIndex === 0) return ` ${ctx.dataset.label}: ${ctx.parsed.y}`;
                            if (ctx.datasetIndex === 1) return ` Z-Score: ${ctx.parsed.y?.toFixed(2) ?? '-'}`;
                            return null; // Sembunyikan tooltip garis WHO
                        }
                    }
                }
            },
            scales: {
                y: {
                    type:     'linear',
                    display:  true,
                    position: 'left',
                    title:    { display: true, text: yLabel[type], color: '#246BCE', font: { weight: 'bold' } },
                    grid:     { color: 'rgba(0,0,0,0.05)' },
                },
                y1: {
                    type:     'linear',
                    display:  true,
                    position: 'right',
                    title:    { display: true, text: 'Z-Score (WHO)', color: '#7C3AED', font: { weight: 'bold' } },
                    min:      -4,
                    max:       4,
                    ticks:    { stepSize: 1 },
                    grid:     { drawOnChartArea: false },
                }
            }
        }
    });
}

async function submitPengukuran(e) {
    e.preventDefault();
    const btn  = document.getElementById('btnSimpan');
    const form = document.getElementById('formPengukuran');
    const fd   = new FormData(form);
    const payload = {};
    fd.forEach((v, k) => { if (v) payload[k] = v; });
    payload.anak_id = parseInt(payload.anak_id);

    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res  = await fetch('{{ route("pengukuran.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            toast(`Pengukuran disimpan! Status gizi: ${data.status_gizi}`, 'success');
            form.reset();
            form.querySelector('[name="tanggal_ukur"]').value = new Date().toISOString().split('T')[0];
            form.querySelector('[name="anak_id"]').value = payload.anak_id;
            setTimeout(() => location.reload(), 1500);
        } else {
            toast(data.message || 'Gagal menyimpan', 'error');
        }
    } catch (err) {
        toast('Koneksi gagal', 'error');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan Pengukuran';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (riwayatData.length > 0) renderChart('bbu');
});
</script>
@endsection
