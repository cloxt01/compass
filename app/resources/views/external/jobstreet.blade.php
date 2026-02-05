@extends('layouts.app')

@section('content')

<h1>Jobstreet Form</h1>

<div id="errors"></div>

<form method="POST" action="{{ route('api.external.passwordless-login', ['provider' => 'jobstreet']) }}" id="sendOtpForm">
    @csrf
    <input type="hidden" name="request_id" id="request_id_send">
    <input type="text" name="email" placeholder="Email" auto-complete="on">
    <button type="submit">Submit</button>
</form> 

<form method="POST" action="{{ route('api.external.verify-otp', ['provider' => 'jobstreet']) }}" id="verifyOtpForm">
    @csrf
    <input type="hidden" name="request_id" id="request_id_verify">
    <input type="hidden" name="email" value="{{ auth()->user()->email }}">
    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
    <input type="text" name="verification_code" placeholder="XXXXXX">
    <button type="submit">Send OTP</button>
</form>

<script>
    async function sendForm(form){
        const formData = new FormData(form);
        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });
        const options = {
            method: 'POST',
            body: JSON.stringify(jsonData),
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        };
        return await fetch(form.action, options);
    }
    function clearElement(){
        const errorElement = document.getElementById('errors');
        errorElement.innerHTML = "";
    }
    function formEvent(event, form){
        event.preventDefault();
        return sendForm(form);
    }
    function handle422(errors){
        Object.keys(errors).forEach(function(field) {
            errors[field].forEach(function(msg) {
                errorElement.innerHTML += '<p>' + msg + '</p>';
            });
        });
    }

    const request_id = crypto.randomUUID();
    const request_id_send = document.getElementById('request_id_send');
    const request_id_verify = document.getElementById('request_id_verify');
    request_id_send.value = request_id;
    request_id_verify.value = request_id;
    const errorElement = document.getElementById('errors');

    

    const formSendOtp = document.getElementById('sendOtpForm');
    const formVerifyOtp = document.getElementById('verifyOtpForm');
    
    formSendOtp.addEventListener(
        'submit',
        async function (event) {
            clearElement();
            event.preventDefault();

            const res = await formEvent(event, formSendOtp);
            const data = await res.json(); 

            switch(res.status){
                case 422:
                    const errors = data.errors;
                    handle422(errors);
                    break;

                default:
                    console.log('Unexpected error');
                    break;
            }
            
        }
    )
    formVerifyOtp.addEventListener(
        'submit',
        function (event) {
            const result = formEvent(event, formVerifyOtp);
            alert(result);
        }
    )

</script>

@endsection
