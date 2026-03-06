<?php

namespace App\Mail;

use App\Models\Devis;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendDevisMail extends Mailable
{
    use Queueable, SerializesModels;

    public $devis;
    public $pdfContent;
    public $recipientName; // Ajouté pour gérer le nom du destinataire unique

    /**
     * Create a new message instance.
     * 
     * @param Devis $devis
     * @param string $pdfContent
     * @param string|null $recipientName (Optionnel : Nom si destinataire unique)
     */
    public function __construct(Devis $devis, $pdfContent, $recipientName = null)
    {
        $this->devis = $devis;
        $this->pdfContent = $pdfContent;
        $this->recipientName = $recipientName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Votre Devis Approlog - {$this->devis->devis_number}")
                    ->view('admin.emails.devis')
                    ->attachData($this->pdfContent, "Devis_{$this->devis->devis_number}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
    }
}