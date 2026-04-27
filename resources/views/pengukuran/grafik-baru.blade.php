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
    .status-box { margin-top:20px; padding:16px; border-radius:8px; border-left:4px solid #10B981; background:#ECFDF5; }
    .status-box.warning { border-left-color:#F59E0B; background:#FEF3C7; }
    .status-box.danger { border-left-color:#DC2626; background:#FEE2E2; }
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
    <div style="position:relative;height:450px;width:100%;">
        <canvas id="growthChart"></canvas>
    </div>
    <div id="statusBox" class="status-box" style="display:none;">
        <div style="display:flex;align-items:center;gap:10px;font-weight:600;">
            <i class="fas fa-check-circle"></i>
            <span id="statusText">Status: Normal</span>
        </div>
        <div id="zscoreText" style="font-size:13px;margin-top:6px;"></div>
    </div>
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
                    <td>{{ \Carbon\Carbon::parse($r['tanggal_ukur'])->format('d/m/Y') }}</td>
                    <td>{{ $r['umur_bulan'] }}</td>
                    <td>{{ $r['bb_kg'] }}</td>
                    <td>{{ $r['tb_pb_cm'] }}</td>
                    <td>{{ $r['lk_cm'] ?? '-' }}</td>
                    <td>{{ number_format($r['z_bbu'], 2) }}</td>
                    <td>{{ number_format($r['z_tbu'], 2) }}</td>
                    <td>{{ number_format($r['z_bbtb'], 2) }}</td>
                    <td>
                        @php
                            $s = $r['overall_8'] ?? 'Belum diukur';
                            $cls = match(true) {
                                $s === 'normal' => 'success',
                                in_array($s, ['kurang', 'sangat_kurang']) => 'warning',
                                in_array($s, ['lebih', 'sangat_lebih']) => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge badge-{{ $cls }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</span>
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

// WHO Growth Reference Data - Lengkap 0-60 bulan (Laki-laki)
const whoData = {
    bbu: [
        [0, 2.1, 2.5, 2.9, 3.3, 3.9, 4.4, 5.0],
        [1, 2.9, 3.4, 3.9, 4.5, 5.1, 5.8, 6.6],
        [2, 3.8, 4.3, 4.9, 5.6, 6.3, 7.1, 8.0],
        [3, 4.4, 5.0, 5.7, 6.4, 7.2, 8.0, 9.0],
        [4, 4.9, 5.6, 6.2, 7.0, 7.8, 8.7, 9.7],
        [5, 5.3, 6.0, 6.7, 7.5, 8.4, 9.3, 10.4],
        [6, 5.7, 6.4, 7.1, 7.9, 8.8, 9.8, 10.9],
        [7, 5.9, 6.7, 7.4, 8.3, 9.2, 10.3, 11.4],
        [8, 6.2, 6.9, 7.7, 8.6, 9.6, 10.7, 11.9],
        [9, 6.4, 7.1, 8.0, 8.9, 9.9, 11.0, 12.3],
        [10, 6.6, 7.4, 8.2, 9.2, 10.2, 11.4, 12.7],
        [11, 6.8, 7.6, 8.4, 9.4, 10.5, 11.7, 13.0],
        [12, 6.9, 7.7, 8.6, 9.6, 10.8, 12.0, 13.3],
        [24, 8.6, 9.7, 10.8, 12.2, 13.6, 15.3, 17.1],
        [36, 10.0, 11.3, 12.7, 14.3, 16.2, 18.3, 20.7],
        [48, 11.2, 12.7, 14.4, 16.3, 18.6, 21.2, 24.2],
        [60, 12.4, 14.1, 16.0, 18.3, 21.0, 24.2, 27.9]
    ],
    tbu: [
        [0, 44.2, 46.1, 48.0, 49.9, 51.8, 53.7, 55.6],
        [1, 48.9, 50.8, 52.8, 54.7, 56.7, 58.6, 60.6],
        [2, 52.4, 54.4, 56.4, 58.4, 60.4, 62.4, 64.4],
        [3, 55.3, 57.3, 59.4, 61.4, 63.5, 65.5, 67.6],
        [4, 57.6, 59.7, 61.8, 63.9, 66.0, 68.0, 70.1],
        [5, 59.6, 61.7, 63.8, 65.9, 68.0, 70.1, 72.2],
        [6, 61.2, 63.3, 65.5, 67.6, 69.8, 71.9, 74.0],
        [12, 68.6, 71.0, 73.4, 75.7, 78.1, 80.5, 82.9],
        [24, 78.7, 81.7, 84.8, 87.8, 90.9, 93.9, 97.0],
        [36, 85.0, 88.7, 92.4, 96.1, 99.8, 103.5, 107.2],
        [48, 90.7, 94.9, 99.1, 103.3, 107.5, 111.7, 115.9],
        [60, 96.1, 100.7, 105.3, 110.0, 114.6, 119.2, 123.9]
    ],
    bbtb: [
        [45.0, 1.9, 2.0, 2.2, 2.4, 2.7, 3.0, 3.3],
        [50.0, 2.6, 2.9, 3.2, 3.6, 4.0, 4.4, 4.9],
        [55.0, 3.6, 4.0, 4.4, 4.9, 5.5, 6.1, 6.7],
        [60.0, 4.8, 5.3, 5.9, 6.5, 7.2, 8.0, 8.8],
        [65.0, 5.8, 6.5, 7.2, 8.0, 8.9, 9.9, 11.0],
        [70.0, 6.6, 7.4, 8.3, 9.2, 10.3, 11.4, 12.7],
        [75.0, 7.4, 8.3, 9.3, 10.4, 11.6, 12.9, 14.3],
        [80.0, 8.2, 9.2, 10.3, 11.5, 12.8, 14.3, 15.9],
        [85.0, 8.9, 10.0, 11.2, 12.5, 14.0, 15.6, 17.4],
        [90.0, 9.6, 10.8, 12.1, 13.5, 15.1, 16.9, 18.8],
        [95.0, 10.3, 11.5, 12.9, 14.5, 16.2, 18.1, 20.2],
        [100.0, 10.9, 12.2, 13.7, 15.4, 17.2, 19.3, 21.5],
        [105.0, 11.5, 12.9, 14.5, 16.2, 18.2, 20.4, 22.8],
        [110.0, 12.1, 13.6, 15.2, 17.1, 19.2, 21.5, 24.1],
        [115.0, 12.7, 14.2, 16.0, 17.9, 20.1, 22.6, 25.3],
        [120.0, 13.3, 14.9, 16.7, 18.7, 21.0, 23.6, 26.5]
    ]
};

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

    const titleMap = {
        bbu:  'Grafik Berat Badan menurut Umur (BB/U) - Laki-laki',
        tbu:  'Grafik Tinggi Badan menurut Umur (TB/U) - Laki-laki',
        bbtb: 'Grafik Berat Badan menurut Tinggi Badan (BB/TB) - Laki-laki'
    };
    const yLabel = { bbu: 'Berat Badan (kg)', tbu: 'Tinggi Badan (cm)', bbtb: 'Berat Badan (kg)' };
    const xLabel = { bbu: 'Umur (bulan)', tbu: 'Umur (bulan)', bbtb: 'Tinggi Badan (cm)' };

    const data_standar = whoData[type];
    let labels = [];
    let datasets = [];

    // Buat kurva WHO
    const sd_minus_3 = [], sd_minus_2 = [], sd_minus_1 = [], median_data = [], sd_plus_1 = [], sd_plus_2 = [], sd_plus_3 = [];
    
    data_standar.forEach(row => {
        const x = row[0];
        labels.push(x);
        sd_minus_3.push({x, y: row[1]});
        sd_minus_2.push({x, y: row[2]});
        sd_minus_1.push({x, y: row[3]});
        median_data.push({x, y: row[4]});
        sd_plus_1.push({x, y: row[5]});
        sd_plus_2.push({x, y: row[6]});
        sd_plus_3.push({x, y: row[7]});
    });

    // Tambah kurva WHO
    datasets.push({label: '-3 SD', data: sd_minus_3, borderColor: '#DC2626', borderDash: [6,3], borderWidth: 2, pointRadius: 0, pointHoverRadius: 0, fill: false, tension: 0.4, showLine: true});
    datasets.push({label: '-2 SD (Kurang)', data: sd_minus_2, borderColor: '#F59E0B', borderDash: [4,3], borderWidth: 2, pointRadius: 0, pointHoverRadius: 0, fill: false, tension: 0.4, showLine: true});
    datasets.push({label: 'Median (Normal)', data: median_data, borderColor: '#10B981', borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 0, fill: false, tension: 0.4, showLine: true});
    datasets.push({label: '+2 SD (Lebih)', data: sd_plus_2, borderColor: '#F59E0B', borderDash: [4,3], borderWidth: 2, pointRadius: 0, pointHoverRadius: 0, fill: false, tension: 0.4, showLine: true});
    datasets.push({label: '+3 SD', data: sd_plus_3, borderColor: '#DC2626', borderDash: [6,3], borderWidth: 2, pointRadius: 0, pointHoverRadius: 0, fill: false, tension: 0.4, showLine: true});

    // Tambah data anak
    const anak_data = [];
    riwayatData.forEach(r => {
        if (type === 'bbtb') {
            anak_data.push({x: r.tb_pb_cm, y: r.bb_kg});
        } else if (type === 'bbu') {
            anak_data.push({x: r.umur_bulan, y: r.bb_kg});
        } else {
            anak_data.push({x: r.umur_bulan, y: r.tb_pb_cm});
        }
    });

    if (anak_data.length > 0) {
        datasets.push({
            label: 'Data Anak',
            data: anak_data,
            borderColor: '#246BCE',
            backgroundColor: '#246BCE',
            borderWidth: 2,
            pointRadius: 6,
            pointBackgroundColor: '#246BCE',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverRadius: 8,
            fill: false,
            tension: 0.4,
        });
    }

    growthChart = new Chart(ctx, {
        type: 'line',
        data: {labels, datasets},
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {mode: 'nearest', intersect: false},
            elements: {
                line: {borderWidth: 2},
                point: {radius: 0, hitRadius: 10, hoverRadius: 6}
            },
            plugins: {
                title: {display: true, text: titleMap[type], font: {size: 14, weight: 'bold'}, color: '#1E3A5F', padding: {bottom: 16}},
                legend: {position: 'bottom', labels: {font: {size: 11}, boxWidth: 15, padding: 12, usePointStyle: true}},
                tooltip: {enabled: true, callbacks: {label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toFixed(1)}`}}
            },
            scales: {
                x: {type: 'linear', title: {display: true, text: xLabel[type], font: {weight: 'bold', size: 12}}, grid: {color: 'rgba(0,0,0,0.05)'}, ticks: {font: {size: 11}}},
                y: {title: {display: true, text: yLabel[type], color: '#246BCE', font: {weight: 'bold', size: 12}}, grid: {color: 'rgba(0,0,0,0.08)', drawBorder: true}, ticks: {font: {size: 11}}}
            }
        }
    });

    // Update status box
    if (riwayatData.length > 0) {
        const latest = riwayatData[riwayatData.length - 1];
        const zMap = {bbu: 'z_bbu', tbu: 'z_tbu', bbtb: 'z_bbtb'};
        const zScore = latest[zMap[type]];
        const statusBox = document.getElementById('statusBox');
        const statusText = document.getElementById('statusText');
        const zscoreText = document.getElementById('zscoreText');
        
        let status = 'Normal', statusColor = '#10B981', statusBg = '#ECFDF5';
        
        if (zScore < -3) {
            status = 'Sangat Kurang';
            statusColor = '#DC2626';
            statusBg = '#FEE2E2';
        } else if (zScore < -2) {
            status = 'Kurang';
            statusColor = '#F59E0B';
            statusBg = '#FEF3C7';
        } else if (zScore > 3) {
            status = 'Sangat Lebih';
            statusColor = '#DC2626';
            statusBg = '#FEE2E2';
        } else if (zScore > 2) {
            status = 'Lebih';
            statusColor = '#F59E0B';
            statusBg = '#FEF3C7';
        }
        
        statusBox.style.display = 'block';
        statusBox.style.borderLeftColor = statusColor;
        statusBox.style.backgroundColor = statusBg;
        statusText.textContent = `Status: ${status}`;
        statusText.style.color = statusColor;
        zscoreText.textContent = `Z-score ${type.toUpperCase()}: ${zScore.toFixed(3)}`;
    }
}

async function submitPengukuran(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSimpan');
    const form = document.getElementById('formPengukuran');
    const fd = new FormData(form);
    const payload = {};
    fd.forEach((v, k) => {if (v) payload[k] = v;});
    payload.anak_id = parseInt(payload.anak_id);

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res = await fetch('{{ route("pengukuran.store") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json'},
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
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan Pengukuran';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (riwayatData.length > 0) renderChart('bbu');
});
</script>
@endsection
