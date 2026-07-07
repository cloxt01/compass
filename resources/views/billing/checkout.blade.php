{{-- resources/views/billing/checkout.blade.php --}}
@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-6">

                <div class="card shadow">

                    <div class="card-header">
                        <h4 class="mb-0">
                            Checkout Subscription
                        </h4>
                    </div>

                    <div class="card-body">

                        <div class="mb-4">

                            <h5>
                                {{ $package->name }}
                            </h5>

                            <p class="text-muted mb-1">
                                Harga
                            </p>

                            <h3>
                                Rp {{ number_format($package->price,0,',','.') }}
                            </h3>

                        </div>

                        <table class="table">

                            <tr>
                                <td>Durasi</td>
                                <td class="text-end">
                                    {{ $package->duration_days }} Hari
                                </td>
                            </tr>

                            <tr>
                                <td>Daily Limit</td>
                                <td class="text-end">
                                    {{ number_format($package->daily_limit) }}
                                </td>
                            </tr>

                            <tr>
                                <td>Monthly Limit</td>
                                <td class="text-end">
                                    {{ number_format($package->monthly_limit) }}
                                </td>
                            </tr>

                        </table>

                        <button
                            class="btn btn-primary w-100"
                            id="btnSubscribe"
                            data-package="{{ $package->id }}">

                            Subscribe Sekarang

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection

@push('scripts')

    <script
        src="{{ config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <script>

        const button = document.getElementById('btnSubscribe');

        button.addEventListener('click', async function () {

            button.disabled = true;
            button.innerHTML = 'Processing...';

            try {

                const response = await fetch(
                    "{{ route('payment.subscribe') }}",
                    {

                        method: 'POST',

                        headers: {

                            'Content-Type': 'application/json',

                            'Accept': 'application/json',

                            'X-CSRF-TOKEN':
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,

                        },

                        body: JSON.stringify({

                            package_id: this.dataset.package

                        })

                    }
                );

                const result = await response.json();

                if (!response.ok) {

                    throw new Error(
                        result.message ?? 'Payment gagal.'
                    );

                }

                snap.pay(result.snap_token, {

                    onSuccess: function (result) {

                        console.log(result);

                        window.location.href = "/billing/success";

                    },

                    onPending: function (result) {

                        console.log(result);

                        alert(
                            "Menunggu pembayaran."
                        );

                    },

                    onError: function (result) {

                        console.log(result);

                        alert(
                            "Pembayaran gagal."
                        );

                    },

                    onClose: function () {

                        alert(
                            "Popup pembayaran ditutup."
                        );

                    }

                });

            } catch (e) {

                alert(e.message);

            } finally {

                button.disabled = false;
                button.innerHTML = 'Subscribe Sekarang';

            }

        });

    </script>

@endpush
