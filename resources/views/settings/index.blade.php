@extends('layouts.settings', ['currentTab' => $tab ?? 'general'])

@section('title', 'Settings · ' . config('ui.brand.name'))
@section('titleNavbar', 'Settings')

{{-- Set Judul Header Kanan Berdasarkan Tab --}}
@section('settings-title')
    {{ ucfirst(str_replace('-', ' ', $tab ?? 'general')) }}
@endsection

{{-- Render Konten Berdasarkan Tab yang Aktif --}}
@section('settings-content')
    @if(($tab ?? 'general') === 'general')
        @include('settings.tabs.general')
    @elseif($tab === 'account')
        @include('settings.tabs.account')
    @elseif($tab === 'security')
        @include('settings.tabs.security')
    @elseif($tab === 'apply-configuration')
        @include('settings.tabs.apply-configuration')
    @endif
@endsection
