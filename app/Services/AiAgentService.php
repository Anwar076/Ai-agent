<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Quote;
use App\Models\Incident;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AiAgentService
{
    private $ollamaUrl;
    private $model;

    public function __construct()
    {
        $this->ollamaUrl = config('ai.ollama_url', 'http://127.0.0.1:11434');
        $this->model = config('ai.model', 'llama2');
    }

    /**
     * Process a user message and generate AI response
     */
    public function processMessage(string $userMessage, Conversation $conversation): string
    {
        try {
            // Get conversation context
            $context = $this->buildConversationContext($conversation);
            
            // Detect intent
            $intent = $this->detectIntent($userMessage);
            
            // Generate AI response based on intent
            $response = $this->generateResponse($userMessage, $context, $intent);
            
            // Handle special actions
            if ($intent['action']) {
                $response = $this->handleSpecialAction($intent, $userMessage, $conversation, $response);
            }
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('AI Agent Error: ' . $e->getMessage());
            return "Hi there! I'm Anwar from Brancom. I'm experiencing some technical difficulties right now, but I'm here to help you. Could you please repeat your question?";
        }
    }

    /**
     * Build conversation context for the AI
     */
    private function buildConversationContext(Conversation $conversation): string
    {
        $messages = $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse();

        $context = "You are Anwar, a friendly and professional customer service representative from Brancom. ";
        $context .= "You help customers with quotes, incident reports, and general inquiries. ";
        $context .= "Always be helpful, professional, and try to understand what the customer needs.\n\n";
        
        if ($conversation->customer_name) {
            $context .= "Customer: {$conversation->customer_name}\n";
        }
        
        $context .= "Conversation history:\n";
        foreach ($messages as $message) {
            $sender = $message->sender === 'ai_agent' ? 'Anwar' : 'Customer';
            $context .= "{$sender}: {$message->content}\n";
        }
        
        return $context;
    }

    /**
     * Detect user intent from message
     */
    private function detectIntent(string $message): array
    {
        $message = strtolower($message);
        
        // Quote keywords
        if (preg_match('/\b(quote|pricing|price|cost|estimate|proposal)\b/', $message)) {
            return ['type' => 'quote', 'action' => 'create_quote'];
        }
        
        // Incident keywords
        if (preg_match('/\b(problem|issue|bug|error|not working|broken|incident|report)\b/', $message)) {
            return ['type' => 'incident', 'action' => 'create_incident'];
        }
        
        // Greeting
        if (preg_match('/\b(hello|hi|hey|good morning|good afternoon|good evening)\b/', $message)) {
            return ['type' => 'greeting', 'action' => null];
        }
        
        return ['type' => 'general', 'action' => null];
    }

    /**
     * Generate AI response using Ollama
     */
    private function generateResponse(string $userMessage, string $context, array $intent): string
    {
        $prompt = $context . "\nCustomer: " . $userMessage . "\nAnwar:";
        
        try {
            $response = Http::timeout(30)->post($this->ollamaUrl . '/api/generate', [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return trim($data['response'] ?? 'I apologize, but I\'m having trouble processing your request right now.');
            }
        } catch (\Exception $e) {
            Log::error('Ollama API Error: ' . $e->getMessage());
        }

        // Fallback responses based on intent
        return $this->getFallbackResponse($intent);
    }

    /**
     * Handle special actions like creating quotes or incidents
     */
    private function handleSpecialAction(array $intent, string $userMessage, Conversation $conversation, string $response): string
    {
        switch ($intent['action']) {
            case 'create_quote':
                return $this->initiateQuoteProcess($userMessage, $conversation);
            
            case 'create_incident':
                return $this->initiateIncidentProcess($userMessage, $conversation);
                
            default:
                return $response;
        }
    }

    /**
     * Initiate quote creation process
     */
    private function initiateQuoteProcess(string $userMessage, Conversation $conversation): string
    {
        // Extract information from the message
        $serviceInfo = $this->extractServiceInformation($userMessage);
        
        // Update conversation type
        $conversation->update(['type' => 'quote']);
        
        if (!empty($serviceInfo)) {
            // If we have enough information, create a quote
            $quote = Quote::create([
                'conversation_id' => $conversation->id,
                'quote_number' => Quote::generateQuoteNumber(),
                'customer_name' => $conversation->customer_name ?? 'Customer',
                'customer_email' => $conversation->customer_email ?? '',
                'customer_phone' => $conversation->customer_phone ?? '',
                'service_description' => $serviceInfo['description'] ?? $userMessage,
                'amount' => $serviceInfo['amount'] ?? 0,
                'valid_until' => Carbon::now()->addDays(30),
                'status' => 'draft'
            ]);
            
            return "Great! I've started preparing a quote for you (Quote #: {$quote->quote_number}). " .
                   "Let me gather a bit more information to provide you with an accurate estimate. " .
                   "Could you please provide more details about the specific services you need?";
        }
        
        return "I'd be happy to prepare a quote for you! To give you the most accurate pricing, " .
               "could you please tell me more about the specific services or products you're interested in?";
    }

    /**
     * Initiate incident reporting process
     */
    private function initiateIncidentProcess(string $userMessage, Conversation $conversation): string
    {
        // Extract incident information
        $incidentInfo = $this->extractIncidentInformation($userMessage);
        
        // Update conversation type
        $conversation->update(['type' => 'incident']);
        
        $incident = Incident::create([
            'conversation_id' => $conversation->id,
            'incident_number' => Incident::generateIncidentNumber(),
            'customer_name' => $conversation->customer_name ?? 'Customer',
            'customer_email' => $conversation->customer_email ?? '',
            'customer_phone' => $conversation->customer_phone ?? '',
            'priority' => $incidentInfo['priority'] ?? 'medium',
            'category' => $incidentInfo['category'] ?? 'general',
            'subject' => $incidentInfo['subject'] ?? 'Customer Issue',
            'description' => $userMessage,
            'status' => 'open'
        ]);
        
        return "I understand you're experiencing an issue. I've created an incident report " .
               "(Incident #: {$incident->incident_number}) to track this for you. " .
               "Let me help you resolve this as quickly as possible. " .
               "Can you provide more details about what exactly is happening?";
    }

    /**
     * Extract service information from user message
     */
    private function extractServiceInformation(string $message): array
    {
        $info = [];
        
        // This is a simple extraction - in a real app, you might use NLP
        if (preg_match('/website|web development|web design/', strtolower($message))) {
            $info['description'] = 'Website Development Services';
            $info['amount'] = 2500.00;
        } elseif (preg_match('/app|mobile|application/', strtolower($message))) {
            $info['description'] = 'Mobile Application Development';
            $info['amount'] = 5000.00;
        } elseif (preg_match('/consulting|consultation/', strtolower($message))) {
            $info['description'] = 'IT Consulting Services';
            $info['amount'] = 150.00; // hourly rate
        }
        
        return $info;
    }

    /**
     * Extract incident information from user message
     */
    private function extractIncidentInformation(string $message): array
    {
        $info = [];
        
        // Determine priority
        if (preg_match('/urgent|critical|down|not working|broken/', strtolower($message))) {
            $info['priority'] = 'high';
        } elseif (preg_match('/slow|minor|small/', strtolower($message))) {
            $info['priority'] = 'low';
        } else {
            $info['priority'] = 'medium';
        }
        
        // Determine category
        if (preg_match('/website|site|page/', strtolower($message))) {
            $info['category'] = 'website';
        } elseif (preg_match('/email|mail/', strtolower($message))) {
            $info['category'] = 'email';
        } elseif (preg_match('/server|hosting/', strtolower($message))) {
            $info['category'] = 'hosting';
        } else {
            $info['category'] = 'technical';
        }
        
        $info['subject'] = 'Customer Reported Issue';
        
        return $info;
    }

    /**
     * Get fallback response when AI is unavailable
     */
    private function getFallbackResponse(array $intent): string
    {
        switch ($intent['type']) {
            case 'greeting':
                return "Hello! I'm Anwar from Brancom. How can I assist you today? I can help you with quotes, technical issues, or answer any questions about our services.";
                
            case 'quote':
                return "I'd be happy to help you with a quote! Could you please tell me more about the services you're interested in?";
                
            case 'incident':
                return "I'm sorry to hear you're experiencing an issue. Let me help you with that. Can you describe what's happening in more detail?";
                
            default:
                return "Thank you for contacting Brancom! I'm Anwar, and I'm here to help. How can I assist you today?";
        }
    }
}