@extends('layouts.app')

@section('header')
    <title>Dashboard</title>
@endsection

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
<a href="{{ route('apply.index') }}">Go to Panel</a>
<br>    
<a href="{{ route('auth.logout') }}">Logout</a>

@if(!empty($appliedJobs) && count($appliedJobs) > 0)


<h3>Recently Applied Jobs</h3>
<table>
    <tr>
        <th>Title</th>
        <th>Company</th>
        <th>Location</th>
        <th>Status</th>
    </tr>
    @if (!empty($appliedJobs) && count($appliedJobs) > 0)
        @foreach ($appliedJobs as $job)
        <tr>
            <td>{{ $job['job_title'] }}</td>
            <td>{{ $job['company'] }}</td>
            <td>{{ $job['job_location'] }}</td>
            <td>{{ $job['status'] }}</td>
        </tr>
        @endforeach
    @endif
</table>
@else
    <p>You haven't applied to any jobs yet.</p>
@endif



@endsection
