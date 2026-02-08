@extends('layouts.app')

@section('content')

<h1>Registration Form</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form id="registrationForm" method="POST" action="{{ route('auth.register') }}">
    @csrf

    <input type="text" name="name" placeholder="Name"><br>
    <input type="text" name="email" placeholder="Email"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <input type="password" name="password_confirmation" placeholder="Confirm Password"><br>
    <button type="submit">Submit</button>
</form>

@endsection
