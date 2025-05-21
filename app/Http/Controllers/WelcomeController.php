<?php

namespace App\Http\Controllers;

use App\Mail\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WelcomeController extends Controller
{
    public function contactUsMail(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'required|string|max:30',
            'message' => 'required|string|max:255',
        ]);
        Mail::to('laravelmybeloved@gmail.com')->send(new ContactUs($validated['name'], $validated['email'], $validated['subject'], $validated['message']));
        Log::info('Email sent to support', ['email' => $validated['email']]);
        \Jeybin\Toastr\Toastr::success('Your eamail sent successfully.')->timeOut(5000)->toast();
        return redirect()->back();
    }
}
