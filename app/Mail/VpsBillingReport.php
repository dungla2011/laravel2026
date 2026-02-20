<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VpsBillingReport extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pdfContent;
    public $fileName;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $pdfContent PDF binary content
     * @param string $fileName PDF file name
     * @return void
     */
    public function __construct(User $user, $pdfContent, $fileName)
    {
        $this->user = $user;
        $this->pdfContent = $pdfContent;
        $this->fileName = $fileName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('VPS Billing Report - ' . now()->format('Y-m-d'))
                    ->view('emails.vps-billing-report')
                    ->with([
                        'user' => $this->user,
                        'date' => now()->format('Y-m-d')
                    ])
                    ->attachData($this->pdfContent, $this->fileName, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
