<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactUs;

class WelcomeController extends Controller
{
    public function contactUsMail(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string',
            'email' =>  'required|email',
            'subject' => 'required|string|max:30',
            'message'=> 'required|string|max:255'
        ]);
        Mail::to('laravelmybeloved@gmail.com')->send(new ContactUs($validated['name'], $validated['email'], $validated['subject'], $validated['message']));
        Log::info('Email sent to support', ['email'=> $validated['email']]);
    }
}
