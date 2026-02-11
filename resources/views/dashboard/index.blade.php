@extends('layouts.app')

@section('header')
@livewireStyles
    <title>Dashboard</title>
@endsection

@section('content')
@livewireScripts
<livewire:dashboard-nav />


<h1>Dashboard</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<h2>Good to see u ,{{ Auth::user()->name }}!</h2>

<livewire:applied-jobs />



<br>
<a href="{{ route('apply.index') }}">Go to Apply</a>
<br>    
<a href="{{ route('auth.logout') }}">Logout</a>


@endsection
