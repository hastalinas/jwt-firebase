<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function send()
    {
        $data = array("name" => "Sherina Eria Hastalina");
        $mail = Mail::send('mail', $data, function ($message) {
            $message->to('hastalinas@gmail.com', 'Sherina eria')->subject('Reset Password');
            $message->from('hastalinas@gmail.com.com', 'Sherina Eria');
        });
        return response()->json([
            'status' => true,
            'message' => 'Mail Send',
        ], 200);
    }
}