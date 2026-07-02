@extends('layouts.app')

@section('title', 'Connect Glints · ' . config('ui.brand.name'))
@section('titleNavbar', 'Platform Connection')

@section('content')
@php
$provider = 'glints';
@endphp
<div class="max-w-xl mx-auto py-10 space-y-6">
    {{-- STATUS & ERROR --}}
    <div id="errors" class="space-y-2 text-sm text-red-600"></div>
    <div id="status" class="text-sm text-gray-600"></div>
    <div id="response" class="text-xs text-gray-500 break-all"></div>

    <div class="ui-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800">
            <h2 class="text-lg font-semibold text-slate-100">
                Login Glints
            </h2>
            <p class="text-sm text-slate-400 mt-1">
                Masukkan email & password dan klik 'login' untuk menghubungkan akunmu
            </p>
        </div>

        <div class="p-6 space-y-6">
            <form method="POST"
                  action="{{ route('api.connection.login', ['provider' => $provider]) }}"
                  id="loginForm"
                  class="space-y-4">
                @csrf

                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">
                        Email
                    </label>
                    <input
                        type="email"
                        name="email"
                        required
                        placeholder="email@example.com"
                        class="ui-input"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">
                        Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        required
                        placeholder="Masukan kata sandi"
                        class="ui-input"
                    >
                </div>

                <button
                    type="submit"
                    class="ui-btn ui-btn-primary w-full">
                    Login Sekarang
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
