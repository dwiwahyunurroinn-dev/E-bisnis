@extends('layouts.admin')

@section('title', 'Dashboard')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@endpush

@section('content')
<div class="cards">
    <div class="stat">
        <div class="lbl"><x-icon name="tag" :size="16"/> Penjualan Hari Ini</div>
        <div class="val sm">Rp{{ number_format($penjualanHariIni, 0, ',', '.') }}</div>
    </div>
    <div class="stat">
        <div class="lbl"><x-icon name="chart" :size="16"/> Penjualan Bulan Ini</div>
        <div class="val sm">Rp{{ number_format($penjualanBulanIni, 0, ',', '.') }}</div>
    </div>
    <div class="stat">
        <div class="lbl"><x-icon name="clock" :size="16"/> Pesanan Pending</div>
        <div class="val">{{ $pesananPending }}</div>
    </div>
    <div class="stat">
        <div class="lbl"><x-icon name="users" :size="16"/> Total Pelanggan</div>
        <div class="val">{{ $totalPelanggan }}</div>
    </div>
</div>

<div class="panel">
    <h2><x-icon name="chart" :size="18"/> Tren Penjualan 7 Hari Terakhir</h2>
    <canvas id="chartPenjualan" height="90"></canvas>
</div>

<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:18px;margin-top:18px;">
    <div class="panel" style="margin-top:0;">
        <h2> Pesanan Terbaru <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a></h2>
        <table>
            <thead><tr><th>Kode</th><th>Pelanggan</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($pesananTerbaru as $p)
                    <tr>
                        <td><a href="{{ route('admin.pesanan.show', $p) }}" style="color:var(--primary);font-weight:700;">{{ $p->kode }}</a></td>
                        <td>{{ $p->user?->name ?? '-' }}</td>
                        <td>Rp{{ number_format($p->total, 0, ',', '.') }}</td>
                        <td><span class="badge b-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-row">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="panel" style="margin-top:0;">
        <h2> Stok Menipis</h2>
        <table>
            <thead><tr><th>Produk</th><th>Stok</th></tr></thead>
            <tbody>
                @forelse ($stokRendah as $s)
                    <tr>
                        <td>{{ $s->nama }}</td>
                        <td><span class="badge {{ $s->stok == 0 ? 'b-batal' : 'b-pending' }}">{{ $s->stok }} unit</span></td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="empty-row">Semua stok aman </td></tr>
                @endforelse
            </tbody>
        </table>
        <a href="{{ route('admin.stok.index') }}" class="btn btn-outline btn-sm btn-block" style="margin-top:12px;width:100%;">Kelola Stok</a>
    </div>
</div>

<script>
    const ctx = document.getElementById('chartPenjualan');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($grafik->pluck('label')),
            datasets: [{
                label: 'Penjualan (Rp)',
                data: @json($grafik->pluck('total')),
                borderColor: '#2c8064',
                backgroundColor: 'rgba(58,161,126,.14)',
                fill: true, tension: .38, pointBackgroundColor: '#2c8064', pointRadius: 4,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp' + v.toLocaleString('id-ID') } } },
            animation: { duration: 900 }
        }
    });
</script>
@endsection
