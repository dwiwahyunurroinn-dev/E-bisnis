@extends('layouts.admin')

@section('title', 'Live Chat — '.$obrolan->namaTampil())

@section('content')
<div class="panel" style="margin-top:0;max-width:720px;">
    <div class="toolbar">
        <h2 style="margin:0;">{{ $obrolan->namaTampil() }}</h2>
        @if ($obrolan->status !== 'selesai')
            <form method="POST" action="{{ route('admin.obrolan.selesai', $obrolan) }}">
                @csrf
                <button class="btn btn-outline btn-sm">Tandai Selesai</button>
            </form>
        @else
            <span class="badge b-off">Selesai</span>
        @endif
    </div>

    <div style="display:flex;flex-direction:column;gap:10px;max-height:460px;overflow-y:auto;padding:6px 2px;margin-bottom:16px;">
        @foreach ($obrolan->pesan as $p)
            @php
                $milikAdmin = $p->pengirim === 'admin';
                $milikBot = $p->pengirim === 'bot';
            @endphp
            <div style="max-width:75%;align-self:{{ $milikAdmin ? 'flex-end' : 'flex-start' }};">
                <div style="font-size:.7rem;color:var(--ink-soft);margin-bottom:3px;{{ $milikAdmin ? 'text-align:right;' : '' }}">
                    {{ $milikAdmin ? 'Admin' : ($milikBot ? 'Bot' : $obrolan->namaTampil()) }} · {{ $p->created_at->format('d M H:i') }}
                </div>
                <div style="padding:10px 14px;border-radius:12px;background:{{ $milikAdmin ? 'var(--primary)' : ($milikBot ? 'var(--bg)' : 'var(--primary-soft)') }};color:{{ $milikAdmin ? '#fff' : 'var(--ink)' }};">
                    {{ $p->pesan }}
                </div>
            </div>
        @endforeach
    </div>

    @if ($obrolan->status !== 'selesai')
        <form method="POST" action="{{ route('admin.obrolan.balas', $obrolan) }}" style="display:flex;gap:10px;">
            @csrf
            <input type="text" name="pesan" placeholder="Tulis balasan..." required style="flex:1;padding:11px 14px;border:1.6px solid var(--line);border-radius:10px;font-family:inherit;">
            <button class="btn btn-primary">Kirim</button>
        </form>
    @endif
</div>
@endsection
