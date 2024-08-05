<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $user;
    public $link_users;

    /**
     * Create a new message instance.
     *
     * @param string $email
     * @param string $user
     * @param string $link_users
     */
    public function __construct($email, $user, $link_users)
    {
        $this->email = $email;
        $this->user = $user;
        $this->link_users = $link_users;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.user-invitation')
            ->subject('Invitation Email')
            ->with([
                'email' => $this->email,
                'user' => $this->user,
                'link_users' => $this->link_users,
            ]);
    }
}
