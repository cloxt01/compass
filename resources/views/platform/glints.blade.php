@extends('layouts.app')

@section('title', 'Connect JobStreet')

@section('content')
@vite('resources/js/app.js')

@php
$provider = 'glints';
@endphp
<div class="max-w-xl mx-auto py-10 space-y-6">
    {{-- STATUS & ERROR --}}
    <div id="errors" class="space-y-2 text-sm text-red-600"></div>
    <div id="status" class="text-sm text-gray-600"></div>
    <div id="response" class="text-xs text-gray-500 break-all"></div>

    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">
            Login Glints
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Masukkan email & password dan klik 'login' untuk menghubungkan akunmu
        </p>
    </div>

    <div class="p-6 space-y-6">

        {{-- SEND OTP --}}
        <form method="POST"
              action="{{ route('api.platform.login', ['provider' => $provider]) }}"
              id="loginForm"
              class="space-y-4">
            @csrf

            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    required
                    placeholder="email@example.com"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-gray-800/30"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    placeholder="Masukan kata sandi"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-gray-800/30"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-md bg-gray-900 text-white py-2 text-sm font-medium
                           hover:bg-gray-800 transition">
                Login Sekarang
            </button>
        </form>

    </div>
</div>
@endsection
