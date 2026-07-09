<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Membuat invoice baru.
     */
    public function generate(Subscription $subscription): Invoice
    {
        return DB::transaction(function () use ($subscription) {

            return Invoice::create([
                'subscription_id' => $subscription->id,
                'invoice_number'  => $this->nextNumber(),
                'status'          => 'pending',
                'subtotal' => $subscription->package_price,
                'discount' => 0,
                'tax'       => 0,
                'total'     => $subscription->package_price,

                'due_date' => now()->addDays(1),
                'notes'    => null,
            ]);

        });
    }

    /**
     * Invoice berikutnya.
     */
    public function nextNumber(): string
    {
        $prefix = now()->format('Ymd');

        $last = Invoice::where('invoice_number', 'like', "INV-{$prefix}-%")
            ->latest('id')
            ->first();

        $number = 1;

        if ($last) {
            $number = ((int) substr($last->invoice_number, -6)) + 1;
        }

        return sprintf(
            'INV-%s-%06d',
            $prefix,
            $number
        );
    }

    /**
     * Tandai invoice lunas.
     */
    public function markPaid(Invoice $invoice): Invoice
    {
        $invoice->update([
            'status' => Invoice::STATUS_PAID,
            'paid_at' => now(),
        ]);

        return $invoice->fresh();
    }

    /**
     * Cancel invoice.
     */
    public function cancel(Invoice $invoice): Invoice
    {
        $invoice->update([
            'status' => Invoice::STATUS_CANCELLED,
        ]);

        return $invoice->fresh();
    }

    /**
     * Tandai overdue.
     */
    public function overdue(Invoice $invoice): Invoice
    {
        $invoice->update([
            'status' => Invoice::STATUS_OVERDUE,
        ]);

        return $invoice->fresh();
    }

    /**
     * Invoice belum dibayar.
     */
    public function pending()
    {
        return Invoice::whereIn('status', [
            'pending',
            'overdue',
        ])->get();
    }

    /**
     * Invoice jatuh tempo.
     */
    public function dueInvoices()
    {
        return Invoice::where('status', 'pending')
            ->whereDate('due_date', '<=', today())
            ->get();
    }

    /**
     * Cari invoice.
     */
    public function find(string $invoiceNumber): Invoice
    {
        return Invoice::where(
            'invoice_number',
            $invoiceNumber
        )->firstOrFail();
    }
}
