@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="panel" style="margin-top:0;">
    <form method="get" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <div class="field" style="margin:0;">
            <label>Dari Tanggal</label>
            <input type="date" name="dari" value="{{ $dari }}">
        </div>
        <div class="field" style="margin:0;">
            <label>Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai }}">
        </div>
        <button class="btn btn-primary">Terapkan</button>
        <a href="{{ route('admin.laporan.ekspor', ['dari'=>$dari,'sampai'=>$sampai]) }}" class="btn btn-outline"> Ekspor CSV</a>
    </form>
</div>

<div class="cards" style="margin-top:18px;">
    <div class="stat"><div class="lbl"> Total Pendapatan</div><div class="val sm">Rp{{ number_format($totalPendapatan,0,',','.') }}</div></div>
    <div class="stat"><div class="lbl"> Pesanan Terbayar</div><div class="val">{{ $totalTerbayar }}</div></div>
    <div class="stat"><div class="lbl"> Total Pesanan</div><div class="val">{{ $totalPesanan }}</div></div>
    <div class="stat"><div class="lbl"> Rata-rata Order</div><div class="val sm">Rp{{ number_format($rataRata,0,',','.') }}</div></div>
</div>

<div class="panel">
    <h2> Rincian Harian</h2>
    <table>
        <thead><tr><th>Tanggal</th><th>Jumlah Pesanan</th><th>Pendapatan</th></tr></thead>
        <tbody>
            @forelse ($perHari as $tgl => $row)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($tgl)->isoFormat('dddd, D MMMM Y') }}</td>
                    <td>{{ $row['jumlah'] }}</td>
                    <td>Rp{{ number_format($row['total'],0,',','.') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="empty-row">Tidak ada transaksi pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
