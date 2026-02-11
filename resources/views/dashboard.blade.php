@extends('layouts.app')

@section('content')

<h1>Dashboard</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<h2>Good to see u ,{{ Auth::user()->name }}!</h2>
<a href="{{ route('apply') }}">Go to Panel</a>
<br>    
<a href="{{ route('auth.logout') }}">Logout</a>


@endsection
