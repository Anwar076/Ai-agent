<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Response;

class QuoteController extends Controller
{
    /**
     * Display a listing of quotes
     */
    public function index()
    {
        $quotes = Quote::with('conversation')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('quotes.index', compact('quotes'));
    }

    /**
     * Display the specified quote
     */
    public function show(Quote $quote)
    {
        $quote->load('conversation');
        return view('quotes.show', compact('quote'));
    }

    /**
     * Download quote as PDF
     */
    public function downloadPdf(Quote $quote)
    {
        $pdf = PDF::loadView('quotes.pdf', compact('quote'));
        
        $filename = "quote-{$quote->quote_number}.pdf";
        
        // Save PDF path if not already saved
        if (!$quote->pdf_path) {
            $pdfPath = "quotes/{$filename}";
            $pdf->save(storage_path("app/public/{$pdfPath}"));
            $quote->update(['pdf_path' => $pdfPath]);
        }

        return $pdf->download($filename);
    }

    /**
     * Send quote via email
     */
    public function sendEmail(Request $request, Quote $quote)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:1000'
        ]);

        try {
            // Generate PDF
            $pdf = PDF::loadView('quotes.pdf', compact('quote'));
            $pdfPath = "quotes/quote-{$quote->quote_number}.pdf";
            $pdf->save(storage_path("app/public/{$pdfPath}"));
            
            // Update quote
            $quote->update([
                'pdf_path' => $pdfPath,
                'status' => 'sent'
            ]);

            // Send email (implement mail class later)
            // Mail::to($request->email)->send(new QuoteMail($quote, $request->message));

            return response()->json([
                'success' => true,
                'message' => 'Quote sent successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send quote: ' . $e->getMessage()
            ], 500);
        }
    }
}
