@extends('layouts.app')

@section('title', 'Applications · Compass')
@section('titleNavbar', 'Applications')

@php
    $isPaused = $user->automation_paused ?? false;
    $totalCount = $applications->total(); // Menggunakan total entri dari pagination
@endphp

@section('content')
    <livewire:application-table />
@endsection
@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Geist', system-ui, sans-serif; background:#0A0A0A; color:#FAFAFA; }
        .font-mono { font-family: 'Geist Mono', monospace !important; }
        .saas-card { background:#111111; border:1px solid #262626; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,.28); transition:all .2s ease; }
        .saas-card:hover { border-color:#333333; }
        .animate-ping { animation: ping 1.6s cubic-bezier(0,0,.2,1) infinite; }
        @keyframes ping { 0% { transform: scale(1); opacity: .6; } 100% { transform: scale(2.4); opacity: 0; } }

        .status-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.2rem .6rem; border-radius:9999px; font-size:.7rem; font-weight:500; border:1px solid rgba(255,255,255,.08); white-space:nowrap; }
        .badge-start { color:#60a5fa; background:rgba(59,130,246,.16); border-color:rgba(59,130,246,.28); }
        .badge-success { color:#22c55e; background:rgba(34,197,94,.16); border-color:rgba(34,197,94,.28); }
        .badge-screening { color: #fbbf24; background: rgba(245, 158, 11, 0.16); border-color: rgba(245, 158, 11, 0.28); }
        .badge-applied { color:#a78bfa; background:rgba(167,139,250,.16); border-color:rgba(167,139,250,.28); }
        .badge-default { color:#a1a1aa; background:rgba(161,161,170,.16); border-color:rgba(161,161,170,.28); }
    </style>
@endpush

