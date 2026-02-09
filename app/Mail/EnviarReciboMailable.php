<?php

namespace App\Mail;

use App\Models\Recibo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnviarReciboMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $recibo;

    public function __construct(Recibo $recibo)
    {
        $this->recibo = $recibo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu comprobante de compra - #' . $this->recibo->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recibo-cliente',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.recibo', ['recibo' => $this->recibo])
                  ->setPaper([0, 0, 226.77, 800], 'portrait');

        return [
            Attachment::fromData(fn () => $pdf->output(), "Ticket_{$this->recibo->id}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}