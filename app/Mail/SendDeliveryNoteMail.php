<?php

namespace App\Mail;

use App\Models\DeliveryNote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendDeliveryNoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $deliveryNote;
    public $pdfContent;
    public $recipientName;

    /**
     * @param DeliveryNote $deliveryNote
     * @param string $pdfContent
     * @param string|null $recipientName
     */
    public function __construct(DeliveryNote $deliveryNote, $pdfContent, $recipientName = null)
    {
        $this->deliveryNote = $deliveryNote;
        $this->pdfContent = $pdfContent;
        $this->recipientName = $recipientName;
    }

    public function build()
    {
        return $this->subject("Bordereau de Livraison Approlog - {$this->deliveryNote->delivery_note_number}")
                    ->view('admin.emails.delivery_note')
                    ->attachData($this->pdfContent, "BL_{$this->deliveryNote->delivery_note_number}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
    }
}