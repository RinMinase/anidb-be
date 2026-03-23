<?php

namespace App\Middleware;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends BaseResetPassword {

  public function toMail($notifiable): MailMessage {
    $url = $this->resetUrl($notifiable);

    return (new MailMessage)
      ->subject('Reset Password Notification')
      ->markdown('email', ['url' => $url]);
  }
}
