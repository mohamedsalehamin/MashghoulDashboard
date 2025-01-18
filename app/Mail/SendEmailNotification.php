<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SendEmailNotification extends Mailable {
    public string $title;

    public string $messageBody;

    /**
     * Create a new message instance.
     */
    public function __construct(string $title, string $messageBody) {
        $this->title = $title;
        $this->messageBody = $messageBody;
    }

    public function build() {
        return $this->view('emails.send_email_notification')
            ->subject($this->title)
            ->with([
                'messageBody' => $this->messageBody,
            ]);
    }
}
