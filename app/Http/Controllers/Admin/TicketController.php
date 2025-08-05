<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactTicket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = ContactTicket::orderBy('created_at', 'desc')->paginate(\App\Helpers\SystemHelper::getPaginationLimit());
        
        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(ContactTicket $ticket)
    {
        return view('admin.tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, ContactTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,closed'
        ]);

        $ticket->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Ticket status updated successfully.');
    }

    public function destroy(ContactTicket $ticket)
    {
        $ticket->delete();
        
        return redirect()->route('admin.tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    public function addNote(Request $request, ContactTicket $ticket)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000'
        ]);

        $currentNotes = $ticket->admin_notes ? $ticket->admin_notes . "\n\n" : "";
        $newNote = "[" . now()->format('M d, Y g:i A') . "] " . $request->admin_note;
        
        $ticket->update([
            'admin_notes' => $currentNotes . $newNote
        ]);

        return redirect()->back()->with('success', 'Note added successfully.');
    }
} 