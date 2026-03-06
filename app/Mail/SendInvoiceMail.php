<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $pdfContent;
    public $recipientName;

    /**
     * @param Invoice $invoice
     * @param string $pdfContent
     * @param string|null $recipientName
     */
    public function __construct(Invoice $invoice, $pdfContent, $recipientName = null)
    {
        $this->invoice = $invoice;
        $this->pdfContent = $pdfContent;
        $this->recipientName = $recipientName;
    }

    public function build()
    {
        return $this->subject("Votre Facture Approlog - {$this->invoice->invoice_number}")
                    ->view('admin.emails.invoice')
                    ->attachData($this->pdfContent, "{$this->invoice->invoice_number}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
    }
}