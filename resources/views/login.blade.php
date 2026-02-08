@extends('layouts.app')

@section('content')

<h1>Login Form</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form id="jobForm" method="POST" action="{{ route('auth.login') }}">
    @csrf

    <input type="text" name="email" placeholder="Email">
    <input type="password" name="password" placeholder="Password">
    <button type="submit">Submit</button>
</form>

<p>Belum punya akun? </p><a href="{{ route('register') }}">Daftar Sekarang</a>

@endsection
