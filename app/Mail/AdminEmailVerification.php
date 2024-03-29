<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

     protected $email_datas;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $email_datas)
    {
        $this->email_datas=$email_datas;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('admin.email.admin_email_verification')
                    ->with([
                        'email_activation_link' => $this->email_datas['email_activation_link'],
                        'email_receiver_name' => $this->email_datas['email_receiver_name'],
                        'user_type' => $this->email_datas['user_type']
                    ]);
    }
}
