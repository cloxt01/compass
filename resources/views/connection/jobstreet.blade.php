@extends('layouts.app')

@section('title', 'Connect JobStreet · ' . config('ui.brand.name'))
@section('titleNavbar', 'Platform Connection')

@section('content')
<div class="max-w-xl mx-auto py-10 space-y-6">

    {{-- STATUS & ERROR --}}
    <div id="errors" class="space-y-2 text-sm text-red-600"></div>
    <div id="status" class="text-sm text-gray-600"></div>
    <div id="response" class="text-xs text-gray-500 break-all"></div>

    {{-- CARD --}}
    <div class="ui-card overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-800">
            <h2 class="text-lg font-semibold text-slate-100">
                Passwordless Login JobStreet
            </h2>
            <p class="text-sm text-slate-400 mt-1">
                Masukkan email, lalu verifikasi dengan OTP
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
                    <label class="block text-sm font-medium text-slate-300 mb-1">
                        Email
                    </label>
                    <input
                        type="email"
                        name="email"
                        required
                        placeholder="email@example.com"
                        class="ui-input"
                    >
                </div>

                <button
                    type="submit"
                    class="ui-btn ui-btn-primary w-full">
                    Kirim OTP
                </button>
            </form>

            <hr class="border-slate-800">

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
                    <label class="block text-sm font-medium text-slate-300 mb-1">
                        Kode OTP
                    </label>
                    <input
                        type="text"
                        name="verification_code"
                        required
                        placeholder="XXXXXX"
                        class="ui-input tracking-widest"
                    >
                </div>

                <button
                    type="submit"
                    class="ui-btn ui-btn-muted w-full">
                    Verifikasi & Login
                </button>
            </form>

        </div>
    </div>
</div>



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


@endsection
