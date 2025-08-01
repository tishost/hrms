<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactTicket;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        // Generate a simple captcha
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $captcha = $num1 . ' + ' . $num2 . ' = ?';
        $expectedAnswer = $num1 + $num2;
        
        return view('contact', compact('captcha', 'expectedAnswer'));
    }

    public function submit(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'details' => 'required|string|max:1000',
            'security_check' => 'required|numeric',
            'expected_answer' => 'required|numeric',
        ]);

        // Captcha validation
        $captchaAnswer = $request->security_check;
        $expectedAnswer = $request->expected_answer;

        if ($captchaAnswer != $expectedAnswer) {
            return back()->withErrors(['security_check' => __('Invalid security check answer.')])->withInput();
        }

        // Create contact ticket
        $ticket = ContactTicket::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'details' => $request->details,
            'status' => 'pending',
            'ticket_number' => 'TKT-' . date('Ymd') . '-' . rand(1000, 9999),
        ]);

        // Send email notification to admin (optional)
        // Mail::to('admin@barimanager.com')->send(new ContactFormSubmitted($ticket));

        return redirect()->back()->with('success', __('Your message has been sent successfully! We will contact you soon.'))->with('ticket_number', $ticket->ticket_number);
    }
} 