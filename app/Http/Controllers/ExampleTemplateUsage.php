<?php

namespace App\Http\Controllers;

use App\Events\UserRegistered;
use App\Events\PaymentCompleted;
use App\Events\SystemNotification;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class ExampleTemplateUsage extends Controller
{
    /**
     * Example: Send welcome email when user registers
     */
    public function registerUser(Request $request)
    {
        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Trigger event - this will automatically send welcome email
        Event::dispatch(new UserRegistered($user));

        return response()->json([
            'message' => 'User registered successfully! Welcome email sent.',
            'user' => $user
        ]);
    }

    /**
     * Example: Send payment confirmation email
     */
    public function processPayment(Request $request)
    {
        // Process payment
        $payment = Payment::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'transaction_id' => 'TXN_' . time(),
            'status' => 'completed',
        ]);

        $user = User::find($request->user_id);

        // Trigger event - this will automatically send payment confirmation email
        Event::dispatch(new PaymentCompleted($user, $payment));

        return response()->json([
            'message' => 'Payment processed successfully! Confirmation email sent.',
            'payment' => $payment
        ]);
    }

    /**
     * Example: Send system notification email
     */
    public function sendSystemNotification(Request $request)
    {
        $user = User::find($request->user_id);

        // Trigger event - this will automatically send system notification email
        Event::dispatch(new SystemNotification($user, [
            'title' => $request->title,
            'message' => $request->message,
        ]));

        return response()->json([
            'message' => 'System notification sent successfully!',
        ]);
    }

    /**
     * Example: Manual email sending (without events)
     */
    public function sendManualEmail(Request $request)
    {
        $user = User::find($request->user_id);
        
        // Find template
        $template = \App\Models\EmailTemplate::where('name', $request->template_name)->first();
        
        if (!$template) {
            return response()->json([
                'error' => 'Template not found'
            ], 404);
        }

        // Send email manually
        \Illuminate\Support\Facades\Mail::to($user->email)
            ->send(new \App\Mail\TemplateEmail($template, [
                'user_name' => $user->name,
                'company_name' => config('app.name'),
                'custom_message' => $request->custom_message,
            ]));

        return response()->json([
            'message' => 'Email sent successfully!',
        ]);
    }
}
