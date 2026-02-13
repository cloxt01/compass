@extends('layouts.appGuest')

@section('title', 'Login - Compass')

@section('content')

<div class="max-w-md mx-auto py-12">

    {{-- CARD --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        {{-- HEADER --}}
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-lg font-semibold text-gray-800">
                Login
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Masuk ke akun Compass kamu
            </p>
        </div>

        {{-- BODY --}}
        <div class="p-6">

            {{-- ERROR --}}
            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input
                        type="email"
                        name="email"
                        required
                        autofocus
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-gray-800/30"
                        placeholder="email@example.com"
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
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-gray-800/30"
                        placeholder="Password"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full rounded-md bg-gray-900 text-white py-2 text-sm font-medium
                           hover:bg-gray-800 transition">
                    Login
                </button>
            </form>
        </div>
    </div>

    {{-- FOOTER --}}
    <p class="text-center text-xs text-gray-500 mt-4">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-gray-900 font-medium hover:underline">
            Daftar sekarang
        </a>
    </p>
</div>

@endsection
