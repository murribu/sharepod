<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

class MailController extends Controller
{
   public function html_email($email_address, $to_name, $subject){
      Mail::send('emails.send_episode', ['name' => $to_name], function($message) use ($email_address, $to_name, $subject) {
         $message->to($email_address, $to_name)
            ->subject($subject);
         $message->from(env('MAILGUN_FROM_EMAIL_ADDRESS', 'shaare.pod@gmail.com'), env('APP_NAME'));
      });
      echo "HTML Email Sent. Check your inbox.";
   }
}
