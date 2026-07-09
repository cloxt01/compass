<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $subscription = $user->getLastActiveSubscription();

        $latestInvoice = null;

        $dailyUsage = 0;
        $monthlyUsage = 0;

        $dailyPercent = 0;
        $monthlyPercent = 0;

        if ($subscription) {

            $latestInvoice = Invoice::where(
                'subscription_id',
                $subscription->id
            )
                ->latest()
                ->first();

            $dailyUsage = $subscription->usages()
                ->whereDate('date', today())
                ->value('apply_count') ?? 0;

            $monthlyUsage = $subscription->usages()
                ->whereBetween('date', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ])
                ->sum('apply_count');

            $dailyPercent = min(
                100,
                ($dailyUsage / max(
                        1,
                        $subscription->package->daily_limit
                    )) * 100
            );

            $monthlyPercent = min(
                100,
                ($monthlyUsage / max(
                        1,
                        $subscription->package->monthly_limit
                    )) * 100
            );
        }

        return view('billing.index', [
            'subscription' => $subscription,
            'invoice' => $latestInvoice,
            'dailyUsage' => $dailyUsage,
            'monthlyUsage' => $monthlyUsage,
            'dailyPercent' => $dailyPercent,
            'monthlyPercent' => $monthlyPercent,
        ]);
    }

    public function show(Invoice $invoice)
    {
        abort_if(
            $invoice->subscription->user_id !== auth()->id(),
            403
        );

        return view('billing.invoice-show', [
            'invoice' => $invoice->load([
                'subscription.package',
                'payments',
            ]),
        ]);
    }
    public function invoices(Request $request)
    {
        $invoices = Invoice::query()
            ->whereHas('subscription', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->latest('id')
            ->paginate(15);

        return view('billing.invoices', [
            'invoices' => $invoices,
        ]);
    }
    public function payments(Request $request)
    {
        $payments = \App\Models\Payment::query()
            ->whereHas('invoice.subscription', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->with('invoice')
            ->latest()
            ->paginate(15);

        return view('billing.payments', [
            'payments' => $payments,
        ]);
    }
    public function packages(Request $request)
    {
        $packages = Package::where('is_active', true)
            ->orderBy('price')
            ->get();

        $subscription = auth()->user()->getLastActiveSubscription();

        return view('billing.packages', [
            'packages' => $packages,
            'subscription' => $subscription,
        ]);
    }

    public function subscription(Request $request)
    {
        $subscription = auth()->user()->getLastActiveSubscription();

        return view('billing.subscription', [
            'subscription' => $subscription,
        ]);
    }

    public function toggleAutoRenew(Request $request)
    {
        $subscription = auth()->user()->getLastActiveSubscription();

        if(!$subscription) {
            return back()->with(
                'error',
                'Auto renew gagal diperbarui, tidak ada subscription aktif'
            );
        }
        $subscription->update([
            'auto_renew' => ! $subscription->auto_renew,
        ]);

        return back()->with(
            'success',
            'Auto renew berhasil diperbarui.'
        );
    }

    public function cancelSubscription(Request $request)
    {
        $subscription = auth()->user()->getLastActiveSubscription();

        if(!$subscription) {
            return back()->with(
                'error',
                'Auto renew gagal diperbarui, tidak ada subscription aktif'
            );
        }

        $subscription->update([
            'auto_renew' => false,
            'status' => Subscription::STATUS_CANCELLED,
        ]);

        return redirect()
            ->route('billing.subscription')
            ->with(
                'success',
                'Subscription berhasil dibatalkan.'
            );
    }
}
