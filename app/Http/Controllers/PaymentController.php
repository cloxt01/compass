<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Package;
use App\Services\Billing\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Billing\PaymentService;
use App\Infrastructure\Contracts\PaymentGateway;


class PaymentController extends Controller
{
    public function __construct(
        protected BillingService $billingService,
        protected PaymentService $paymentService
    ) {
    }

    public function webhook(Request $request)
    {

        $success = $this->paymentService->callback($request->all());
        if ($success) {
            Log::info('Webhook berhasil diproses.');
            Log::info('Webhook payload: ', $request->all());
        } else {
            Log::warning('Webhook gagal diproses.');
        }
        return response()->json([
            'success' => $success ? true : false,
        ]);
    }
    public function subscribe(
        Request $request
    )
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);
        $package = Package::findOrFail(
            $request->package_id
        );

        return response()->json(
            $this->billingService->subscribe(
                $request->user(),
                $package
            )
        );
    }
    public function pay(Invoice $invoice)
    {
        abort_if(
            $invoice->subscription->user_id !== auth()->id(),
            403
        );

        $payment = $invoice->payments()
            ->latest()
            ->firstOrFail();

        if ($payment->status === 'paid') {
            return redirect()
                ->route('billing.invoices')
                ->with('success', 'Invoice sudah dibayar.');
        }

        return view('billing.pay', [
            'invoice' => $invoice,
            'payment' => $payment,
        ]);
    }

    public function finish(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:invoices,invoice_number',
            'status_code' => 'required|integer',
            'transaction_status' => 'required|string',
        ]);
        $invoice = Invoice::where('invoice_number', $request->order_id)->firstOrFail();


        return view('billing.payment-finish', compact('invoice'));
    }
}
