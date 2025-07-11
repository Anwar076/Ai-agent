<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\AiAgentService;

class ChatInterface extends Component
{
    public $conversationId = null;
    public $messages = [];
    public $newMessage = '';
    public $isTyping = false;
    public $showWelcome = true;
    public $customerName = '';
    public $customerEmail = '';
    public $customerPhone = '';
    public $showActions = false;

    protected $aiAgentService;

    public function boot(AiAgentService $aiAgentService)
    {
        $this->aiAgentService = $aiAgentService;
    }

    protected $rules = [
        'customerName' => 'required|string|max:100',
        'customerEmail' => 'required|email|max:100',
        'customerPhone' => 'nullable|string|max:20',
        'newMessage' => 'required|string|max:1000',
    ];

    public function mount()
    {
        // Initialize component
    }

    public function startConversation()
    {
        $this->validate([
            'customerName' => 'required|string|max:100',
            'customerEmail' => 'required|email|max:100',
            'customerPhone' => 'nullable|string|max:20',
        ]);

        // Create new conversation
        $conversation = Conversation::create([
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'customer_phone' => $this->customerPhone,
            'status' => 'active',
            'type' => 'general'
        ]);

        $this->conversationId = $conversation->id;
        $this->showWelcome = false;

        // Send welcome message
        $welcomeMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender' => 'ai_agent',
            'content' => "Hello {$this->customerName}! I'm Anwar from Brancom. How can I assist you today? I can help you with quotes, technical issues, or answer any questions about our services.",
            'message_type' => 'text',
            'is_processed' => true
        ]);

        $this->messages = [$welcomeMessage];
        $this->showActions = true;
    }

    public function sendMessage()
    {
        $this->validate(['newMessage' => 'required|string|max:1000']);

        if (!$this->conversationId) {
            return;
        }

        $conversation = Conversation::find($this->conversationId);
        
        // Save user message
        $userMessage = Message::create([
            'conversation_id' => $this->conversationId,
            'sender' => 'user',
            'content' => $this->newMessage,
            'message_type' => 'text'
        ]);

        $this->messages[] = $userMessage;
        $messageContent = $this->newMessage;
        $this->newMessage = '';
        $this->isTyping = true;

        // Simulate typing delay and get AI response
        $this->dispatch('message-sent');
        
        try {
            $aiResponse = $this->aiAgentService->processMessage($messageContent, $conversation);
            
            // Simulate typing delay
            sleep(1);
            
            $aiMessage = Message::create([
                'conversation_id' => $this->conversationId,
                'sender' => 'ai_agent',
                'content' => $aiResponse,
                'message_type' => 'text',
                'is_processed' => true
            ]);

            $this->messages[] = $aiMessage;
            $this->isTyping = false;
            
        } catch (\Exception $e) {
            \Log::error('Livewire Chat Error: ' . $e->getMessage());
            
            $aiMessage = Message::create([
                'conversation_id' => $this->conversationId,
                'sender' => 'ai_agent',
                'content' => "I apologize, but I'm experiencing some technical difficulties. Please try again in a moment.",
                'message_type' => 'text',
                'is_processed' => false
            ]);

            $this->messages[] = $aiMessage;
            $this->isTyping = false;
        }
    }

    public function requestQuote()
    {
        $this->newMessage = "I would like to request a quote for your services.";
        $this->sendMessage();
    }

    public function reportIssue()
    {
        $this->newMessage = "I need to report an issue or problem.";
        $this->sendMessage();
    }

    public function askGeneralQuestion()
    {
        $this->showActions = false;
    }

    public function endConversation()
    {
        if ($this->conversationId) {
            $conversation = Conversation::find($this->conversationId);
            $conversation->update(['status' => 'completed']);

            $goodbyeMessage = Message::create([
                'conversation_id' => $this->conversationId,
                'sender' => 'ai_agent',
                'content' => "Thank you for contacting Brancom! If you need any further assistance, please don't hesitate to reach out. Have a great day!",
                'message_type' => 'text',
                'is_processed' => true
            ]);

            $this->messages[] = $goodbyeMessage;
        }

        // Reset the chat
        $this->reset();
        $this->showWelcome = true;
    }

    public function render()
    {
        return view('livewire.chat-interface');
    }
}
