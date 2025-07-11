<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\AiAgentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ChatController extends Controller
{
    protected $aiAgentService;

    public function __construct(AiAgentService $aiAgentService)
    {
        $this->aiAgentService = $aiAgentService;
    }

    /**
     * Display the chat interface
     */
    public function index(): View
    {
        return view('chat.index');
    }

    /**
     * Start a new conversation
     */
    public function startConversation(Request $request): JsonResponse
    {
        $conversation = Conversation::create([
            'customer_name' => $request->input('name'),
            'customer_email' => $request->input('email'),
            'customer_phone' => $request->input('phone'),
            'status' => 'active',
            'type' => 'general'
        ]);

        // Send welcome message
        $welcomeMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender' => 'ai_agent',
            'content' => "Hello" . ($conversation->customer_name ? " {$conversation->customer_name}" : "") . "! I'm Anwar from Brancom. How can I assist you today? I can help you with quotes, technical issues, or answer any questions about our services.",
            'message_type' => 'text',
            'is_processed' => true
        ]);

        return response()->json([
            'conversation_id' => $conversation->id,
            'message' => $welcomeMessage
        ]);
    }

    /**
     * Send a message and get AI response
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string|max:1000'
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);
        
        // Save user message
        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender' => 'user',
            'content' => $request->message,
            'message_type' => 'text'
        ]);

        try {
            // Get AI response
            $aiResponse = $this->aiAgentService->processMessage($request->message, $conversation);
            
            // Save AI response
            $aiMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender' => 'ai_agent',
                'content' => $aiResponse,
                'message_type' => 'text',
                'is_processed' => true
            ]);

            return response()->json([
                'user_message' => $userMessage,
                'ai_message' => $aiMessage,
                'typing' => false
            ]);

        } catch (\Exception $e) {
            \Log::error('Chat Error: ' . $e->getMessage());
            
            // Fallback response
            $aiMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender' => 'ai_agent',
                'content' => "I apologize, but I'm experiencing some technical difficulties. Please try again in a moment, or feel free to contact us directly.",
                'message_type' => 'text',
                'is_processed' => false
            ]);

            return response()->json([
                'user_message' => $userMessage,
                'ai_message' => $aiMessage,
                'typing' => false,
                'error' => true
            ]);
        }
    }

    /**
     * Get conversation history
     */
    public function getConversation(Request $request): JsonResponse
    {
        $conversation = Conversation::with(['messages' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])->findOrFail($request->conversation_id);

        return response()->json($conversation);
    }

    /**
     * Typing indicator endpoint
     */
    public function typing(Request $request): JsonResponse
    {
        // This could be used with WebSockets for real-time typing indicators
        return response()->json(['status' => 'typing']);
    }

    /**
     * End conversation
     */
    public function endConversation(Request $request): JsonResponse
    {
        $conversation = Conversation::findOrFail($request->conversation_id);
        $conversation->update(['status' => 'completed']);

        // Send goodbye message
        $goodbyeMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender' => 'ai_agent',
            'content' => "Thank you for contacting Brancom! If you need any further assistance, please don't hesitate to reach out. Have a great day!",
            'message_type' => 'text',
            'is_processed' => true
        ]);

        return response()->json([
            'message' => $goodbyeMessage,
            'conversation_ended' => true
        ]);
    }
}
