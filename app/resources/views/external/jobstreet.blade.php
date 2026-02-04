@extends('layouts.app')

@section('content')

<h1>Jobstreet Form</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

{{-- FORM 1: SEND OTP (ASYNC / FETCH) --}}
<form method="POST" action="{{ route('api.external.send-otp', ['provider' => 'jobstreet']) }}" id="sendOtpForm">
    @csrf
    <input type="hidden" name="uuid" id="uuid">
    <input type="text" name="email" placeholder="Email">
    <button type="submit">Submit</button>
</form> 

{{-- FORM 2: VERIFY OTP (NORMAL SUBMIT, BIAR $errors HIDUP) --}}
<form method="POST" action="{{ route('api.external.verify-otp', ['provider' => 'jobstreet']) }}">
    @csrf
    <input type="hidden" name="uuid" id="uuid_otp">
    <input type="hidden" name="email" value="{{ auth()->user()->email }}">
    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
    <input type="text" name="code" placeholder="XXXXXX">
    <button type="submit">Send OTP</button>
</form>

<script>
    // satu UUID, konsisten
    const uuid = crypto.randomUUID();
    document.getElementById('uuid').value = uuid;
    document.getElementById('uuid_otp').value = uuid;

    // FORM 1 AJA yang pakai fetch
    const connectForm = document.getElementById('sendOtpForm');
    connectForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const res = await fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'X-CSRF-TOKEN': this.querySelector('input[name=_token]').value,
                'Accept': 'application/json'
            },
            // send cookies so the session-based CSRF token and auth session are available
            credentials: 'same-origin'
        });

        const text = await res.text();
        let body = text;
        try { body = JSON.parse(text); } catch (_) {}

        if (!res.ok) {
            alert(body.message || 'Failed sending OTP');
            return;
        }

        alert('OTP sent');
    });
</script>

@endsection
