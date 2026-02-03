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

<form method="POST" action="{{ route('api.external.send_otp', ['provider' => 'jobstreet']) }}">
    @csrf
    <input type="hidden" name="uuid" id="uuid">
    <input type="text" name="email" placeholder="Email">
    <button type="submit">Submit</button>
</form> 

<form method="POST" action="{{ route('api.external.verify_otp', ['provider' => 'jobstreet']) }}">
    @csrf
    <input type="hidden" name="uuid" id="uuid_otp">
    <input type="hidden" name="user_id" id="user_id" value="{{ auth()->id() }}">
    <input type="text" name="otp" placeholder="XXXXXX">
    <button type="submit">Send OTP</button>
</form>

<script>
    const uuid = crypto.randomUUID();
    document.getElementById('uuid').value = uuid;
    document.getElementById('uuid_otp').value = uuid;

    // cegah refresh form pertama — post ke API route dan tangani JSON respons
    const connectForm = document.querySelector('form[action="{{ route('api.external.send_otp', ['provider' => 'jobstreet']) }}"]');
    connectForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const res = await fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
        'X-CSRF-TOKEN': this.querySelector('input[name=_token]').value,
        'Accept': 'application/json'
        }
    });

    const text = await res.text();
    let body = text;
    try { body = JSON.parse(text); } catch (err) { /* leave as text */ }

    if (!res.ok) {
        console.error('Connect API error', res.status, body);
        alert('Failed to submit: ' + (body.message || res.statusText || 'Unknown'));
        return;
    }

    // Success: give simple feedback
    alert('Sent, server response: ' + (body.message || JSON.stringify(body)));
    });

    // cegah refresh form kedua (OTP) — handle JSON response and show errors if any
    const otpForm = document.querySelector('form[action="{{ route('internal.send_login_otp') }}"]');
    otpForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const uuidVal = this.querySelector('input[name="uuid"]').value || '';
    const otpVal = this.querySelector('input[name="otp"]').value || '';
    if (!uuidVal) { alert('Internal error: uuid not set. Please reload the page.'); return; }
    if (!otpVal) { alert('Please enter the OTP first.'); return; }

    const res = await fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
        'X-CSRF-TOKEN': this.querySelector('input[name=_token]').value,
        'Accept': 'application/json'
        }
    });

    const text = await res.text();
    let body = text;
    try { body = JSON.parse(text); } catch (err) { /* leave as text */ }

    if (!res.ok) {
        console.error('OTP API error', res.status, body);
        alert('Failed to send OTP: ' + (body.message || res.statusText || 'Unknown'));
        return;
    }

    alert('OTP sent: ' + (body.status || JSON.stringify(body)));
    });
</script>

@endsection
