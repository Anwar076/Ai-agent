<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Quote;
use App\Models\Incident;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_conversations' => Conversation::count(),
            'active_conversations' => Conversation::where('status', 'active')->count(),
            'total_quotes' => Quote::count(),
            'pending_quotes' => Quote::where('status', 'draft')->count(),
            'total_incidents' => Incident::count(),
            'open_incidents' => Incident::whereIn('status', ['open', 'in_progress'])->count(),
        ];

        $recentConversations = Conversation::with('latestMessage')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        $urgentIncidents = Incident::where('priority', 'urgent')
            ->orWhere('priority', 'high')
            ->where('status', '!=', 'resolved')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentConversations', 'urgentIncidents'));
    }

    /**
     * Show all conversations
     */
    public function conversations(Request $request)
    {
        $query = Conversation::with(['latestMessage', 'quotes', 'incidents']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        $conversations = $query->orderBy('updated_at', 'desc')->paginate(20);

        return view('admin.conversations', compact('conversations'));
    }

    /**
     * Show all quotes
     */
    public function quotes(Request $request)
    {
        $query = Quote::with('conversation');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.quotes', compact('quotes'));
    }

    /**
     * Show all incidents
     */
    public function incidents(Request $request)
    {
        $query = Incident::with('conversation');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        $incidents = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.incidents', compact('incidents'));
    }

    /**
     * Show a specific conversation
     */
    public function showConversation(Conversation $conversation)
    {
        $conversation->load(['messages' => function($query) {
            $query->orderBy('created_at', 'asc');
        }, 'quotes', 'incidents']);

        return view('admin.conversation', compact('conversation'));
    }
}
