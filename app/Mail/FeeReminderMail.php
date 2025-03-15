<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeeReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The email data.
     *
     * @var array
     */
    public $emailData;

    /**
     * Create a new message instance.
     *
     * @param array $emailData
     * @return void
     */
    public function __construct(array $emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailData['subject'])
                    ->view('emails.fee_reminder')
                    ->with([
                        'student' => $this->emailData['student'],
                        'fees' => $this->emailData['fees'],
                        'total_amount' => $this->emailData['total_amount'],
                        'total_paid' => $this->emailData['total_paid'],
                        'balance' => $this->emailData['balance'],
                        'subject' => $this->emailData['subject'],
                        'message' => $this->emailData['message'],
                    ]);
    }
}