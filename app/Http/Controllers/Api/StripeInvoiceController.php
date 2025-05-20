<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripeInvoiceController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function getInvoices(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
            'starting_after' => 'nullable|string',
            'ending_before' => 'nullable|string',
        ]);

        $invoices = $this->stripeService->getInvoices(
            $request->get('limit', 10),
            $request->get('starting_after'),
            $request->get('ending_before')
        );

        return response()->json($invoices);
    }

    public function getInvoice(Request $request, $invoiceId)
    {
        $invoice = $this->stripeService->getInvoice($invoiceId);

        return response()->json($invoice);
    }

    public function payInvoice(Request $request, $invoiceId)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $invoice = $this->stripeService->payInvoice($invoiceId, $request->payment_method);

        return response()->json($invoice);
    }

    public function sendInvoice(Request $request, $invoiceId)
    {
        $this->stripeService->sendInvoice($invoiceId);

        return response()->json(['message' => 'Invoice sent successfully']);
    }
}
