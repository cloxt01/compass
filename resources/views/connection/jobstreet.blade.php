@extends('layouts.app')

@section('title', 'Connect JobStreet · Compass')
@section('titleNavbar', 'Platform Connection')

@section('content')
    <div class="mx-auto max-w-[600px] space-y-6 px-1 pb-6 pt-6">

        {{-- STATUS & ERROR --}}
        <div id="errors" class="space-y-2 text-sm text-red-400 empty:hidden"></div>
        <div id="status" class="text-sm text-blue-400 empty:hidden"></div>
        <div id="response" class="break-all font-mono text-xs text-[#a1a1aa] empty:hidden"></div>

        {{-- CARD --}}
        <div class="saas-card overflow-hidden">

            <div class="border-b border-[#262626] px-6 py-5">
                <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">
                    Passwordless Login JobStreet
                </h2>
                <p class="mt-1 text-sm text-[#a1a1aa]">
                    Masukkan email, lalu verifikasi menggunakan kode OTP
                </p>
            </div>

            <div class="p-6 space-y-6">

                {{-- SEND OTP --}}
                <form method="POST"
                      action="{{ route('api.connection.passwordless-login', ['provider' => 'jobstreet']) }}"
                      id="sendOtpForm"
                      class="space-y-4">
                    @csrf

                    <input type="hidden" name="request_id" id="request_id_send">
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                            Email Address
                        </label>
                        <input
                            type="email"
                            name="email"
                            required
                            placeholder="name@example.com"
                            class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]"
                        >
                    </div>

                    <button
                        type="submit"
                        class="h-11 w-full cursor-pointer rounded-xl bg-white/85 text-sm font-semibold text-black transition hover:bg-white">
                        Kirim OTP
                    </button>
                </form>

                {{-- SPACER SUBSTITUTION FOR HR TAG --}}
                <div class="py-1"></div>

                {{-- VERIFY OTP --}}
                <form method="POST"
                      action="{{ route('api.connection.verify-otp', ['provider' => 'jobstreet']) }}"
                      id="verifyOtpForm"
                      class="space-y-4">
                    @csrf

                    <input type="hidden" name="request_id" id="request_id_verify">
                    <input type="hidden" name="email" id="verifyEmailInput">
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                            Kode OTP
                        </label>
                        <input
                            type="text"
                            name="verification_code"
                            required
                            placeholder="XXXXXX"
                            class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a] tracking-widest font-mono"
                        >
                    </div>

                    <button
                        type="submit"
                        class="h-11 w-full cursor-pointer rounded-xl border border-[#262626] bg-[#111111] text-sm font-semibold text-[#fafafa] transition hover:bg-[#1c1c1c] hover:border-[#333]">
                        Verifikasi & Login
                    </button>
                </form>

            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('apply') }}" class="text-xs text-[#a1a1aa] hover:text-[#fafafa] transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Panel Utama
            </a>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Geist', system-ui, sans-serif; background:#0A0A0A; color:#FAFAFA; }
        .font-mono { font-family: 'Geist Mono', monospace !important; }
        .saas-card { background:#111111; border:1px solid #262626; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,.28); transition:all .2s ease; }
        .saas-card:hover { border-color:#333333; }
        .saas-input { border:1px solid #262626; background:#0A0A0A; outline:0; transition:.2s ease; }
        .saas-input:focus { border-color:#3B82F6; box-shadow:0 0 0 2px rgba(59,130,246,.15); }
    </style>
@endpush

<script>
    // UTILS
    function delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // HTTP
    async function request(url, method = 'POST', data = null) {
        try {
            const res = await axios({ url, method, data, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, withCredentials: true });
            return { status: res.status, data: res.data };
        } catch (err) {
            if (err.response) {
                return { status: err.response.status, errors: err.response.data.errors || {} };
            }
            return { status: 0, errors: { network: ['Network error'] } };
        }
    }

    async function sendForm(form) {
        const formData = new FormData(form);
        const jsonData = {};
        formData.forEach((value, key) => { jsonData[key] = value; });
        return await request(form.action, 'POST', jsonData);
    }
    async function requestInfo(id) {
        const url = `${window.location.origin}/api/request/${encodeURIComponent(id)}`;
        return await request(url, 'GET');
    }
    async function saveToken(token, provider) {
        const url = `${window.location.origin}/platform/${provider}/save-token`;
        return await request(url, 'GET');
    }

    // DOM

    function clearElement(){
        document.getElementById('errors').innerHTML = "";
        document.getElementById('response').innerHTML = ""
    }
    async function formEvent(event, form){
        event.preventDefault();
        return await sendForm(form);
    }
    function displayErrors(errors, container){
        Object.keys(errors).forEach(function(field) {
            errors[field].forEach(function(msg) {
                container.innerHTML += '<p>' + msg + '</p>';
            });
        });
    }
    function displayResponse(data){
        if(typeof data === 'object'){
            data = JSON.stringify(data);
        }

        const responseElement = document.getElementById('response');
        responseElement.innerHTML = '<p>' + data + '</p>';
    }
    function displayStatus(status){
        const statusElement = document.getElementById('status');
        statusElement.innerHTML = '<p>' + status + '</p>';
    }

    // LOGIC
    async function startPolling() {
        while (polling_running) {
            const res = await requestInfo(request_id);
            if (res.status === 200) {
                const req = res.data;
                const status = req.status;
                console.log(req);
                displayStatus(status);
                if (status === 'LOGIN_SUCCESS'){
                    polling_running = false;

                    console.log(req);
                    const {id, token:payload, provider} = req.data;
                    console.log(id, payload, provider);
                    let url = `${window.location.origin}/platform/${provider}/save-token`;
                    const saved = await request(url, 'POST', {token: payload});
                    displayResponse(saved);
                    if(saved.status === 200 && saved.data.redirect){
                        window.location.href = saved.data.redirect;
                    }
                }


            } else {
                displayErrors(res.errors, errorElement);
            }
            await delay(3000);

        }
    }

    const request_id = crypto.randomUUID();
    const request_id_send = document.getElementById('request_id_send');
    const request_id_verify = document.getElementById('request_id_verify');
    request_id_send.value = request_id;
    request_id_verify.value = request_id;
    const errorElement = document.getElementById('errors');
    const verifyEmailInput = document.getElementById('verifyEmailInput');



    const formSendOtp = document.getElementById('sendOtpForm');
    const formVerifyOtp = document.getElementById('verifyOtpForm');
    let polling_running = false



    formSendOtp.addEventListener(
        'submit',
        async function (event) {
            clearElement();
            event.preventDefault();

            const res = await formEvent(event, formSendOtp);
            switch(res.status){
                case 422:
                case 500:
                    const errors = res.errors;
                    displayErrors(errors, errorElement);
                    break;
                case 200:
                    displayResponse(res.data);
                    break;

                default:
                    console.log('Unexpected error');
                    break;
            }

        }
    )
    formVerifyOtp.addEventListener(
        'submit',
        async function (event) {
            clearElement();
            event.preventDefault();

            const email = formSendOtp.querySelector('input[name="email"]').value;
            verifyEmailInput.value = email;

            const res = await formEvent(event, formVerifyOtp);

            switch(res.status){
                case 400:
                case 422:
                case 500:
                    const errors = res.errors;
                    displayErrors(errors, errorElement);
                    break;
                case 200:
                    displayResponse(res.data);
                    polling_running = true
                    startPolling();
                    break;
                default:
                    console.log('Unexpected error');
                    break;
            }

        }
    )
</script>
