@extends('layouts.app')

@section('content')

@php
    $hasJobstreet = auth()->user()->jobstreetAccount && auth()->user()->jobstreetAccount->access_token;
@endphp

<h1>Connection</h1>

<table>
    <tr>
        <th>Platform</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <tr>
        <td>Jobstreet</td>
        <td>{{ $hasJobstreet ? 'Connected' : 'Not Connected' }}</td>
        <td>{!! $hasJobstreet ? '<a href="'.route('api.platform.disconnect', ['provider' => 'jobstreet']).'">Disconnect</a>' : '<a href="'.route('platform.connect.jobstreet').'">Connect</a>' !!}</td>
    </tr>
    <tr>
        <td>Glints</td>
        <td>Unavailable</td>
        <td><a href="#"></a></td>
    </tr>
</table>

@endsection


