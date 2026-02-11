@extends('layouts.app')

@section('content')

<h1>Apply</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
<form id="panelForm" method="POST" action="{{ route('apply.start') }}">
    @csrf

    <input name="keyword" placeholder="Keyword">
    <input name="location" placeholder="Lokasi">
    <input name="pageSize" type="number" value="5">
    <input name="interval" type="number" value="5">
    <input name="max_applications" type="number" value="10">

    <br>

    @php
        $hasJobstreet = $user->jobstreetAccount && $user->jobstreetAccount->access_token;
    @endphp

    <label>
        <input type="checkbox" 
            name="providers[]" 
            value="jobstreet" 
            {{ $hasJobstreet ? 'checked' : '' }} 
            {{ $hasJobstreet ? '' : 'disabled' }}>
        JobStreet {!! $hasJobstreet ? '' : '<i>(Connect your account first)</i>' !!}
    </label>
    <br>

    <button type="submit">Mulai</button>
</form>

<a href="{{ route('platform.index') }}">Connect Accounts</a>


@endsection
