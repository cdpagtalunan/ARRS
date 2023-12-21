<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

class EmailController extends Controller
{
    public function send_email(Request $request){
        $data = [
            'ticket_number' => '1111',
            'name'          => 'test',
            'mensahe'       => 'mensahe',
        ];
        Mail::send('mail.mailer', $data, function($message) use ( $request){
            // $message->to($to_email);
            $message->cc('cpagtalunan@pricon.ph');
            $message->bcc(['mclegaspi@pricon.ph', 'jdomingo@pricon.ph']);

            $message->subject("Reconciliation Request For Approval");
        });
    }
}
