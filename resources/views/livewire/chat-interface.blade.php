<div class="flex flex-col h-screen max-w-4xl mx-auto bg-white shadow-lg">
    <!-- Header -->
    <div class="bg-blue-600 text-white p-4 rounded-t-lg">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold">Anwar - Brancom Support</h3>
                <p class="text-sm text-blue-200">AI Customer Service Agent</p>
            </div>
            @if($conversationId)
                <div class="ml-auto">
                    <button wire:click="endConversation" class="text-blue-200 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Chat Content -->
    <div class="flex-1 flex flex-col">
        @if($showWelcome)
            <!-- Welcome Form -->
            <div class="flex-1 flex items-center justify-center p-6">
                <div class="bg-gray-50 rounded-lg p-8 max-w-md w-full">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Welcome to Brancom</h2>
                        <p class="text-gray-600 mt-2">Hi! I'm Anwar, your AI customer service agent. Let's get started!</p>
                    </div>

                    <form wire:submit="startConversation" class="space-y-4">
                        <div>
                            <label for="customerName" class="block text-sm font-medium text-gray-700 mb-2">Your Name *</label>
                            <input type="text" id="customerName" wire:model="customerName" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter your name" required>
                            @error('customerName') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="customerEmail" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" id="customerEmail" wire:model="customerEmail" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter your email" required>
                            @error('customerEmail') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="customerPhone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number (Optional)</label>
                            <input type="tel" id="customerPhone" wire:model="customerPhone" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter your phone number">
                            @error('customerPhone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Start Chat
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4" x-ref="messagesContainer" x-init="$refs.messagesContainer.scrollTop = $refs.messagesContainer.scrollHeight">
                @foreach($messages as $message)
                    <div class="flex {{ $message->sender === 'ai_agent' ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender === 'ai_agent' ? 'bg-gray-200 text-gray-800' : 'bg-blue-600 text-white' }}">
                            @if($message->sender === 'ai_agent')
                                <p class="text-xs text-gray-500 mb-1">Anwar</p>
                            @endif
                            <p class="text-sm">{{ $message->content }}</p>
                            <p class="text-xs mt-1 {{ $message->sender === 'ai_agent' ? 'text-gray-500' : 'text-blue-200' }}">
                                {{ $message->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                @endforeach

                @if($isTyping)
                    <div class="flex justify-start">
                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg bg-gray-200 text-gray-800">
                            <p class="text-xs text-gray-500 mb-1">Anwar</p>
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Anwar is typing...</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quick Action Buttons -->
            @if($showActions)
                <div class="p-4 border-t border-gray-200 bg-gray-50">
                    <p class="text-sm text-gray-600 mb-3">How can I help you today?</p>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="requestQuote" 
                                class="bg-green-100 text-green-800 px-3 py-2 rounded-lg text-sm hover:bg-green-200 transition-colors">
                            💰 Request a Quote
                        </button>
                        <button wire:click="reportIssue" 
                                class="bg-red-100 text-red-800 px-3 py-2 rounded-lg text-sm hover:bg-red-200 transition-colors">
                            🔧 Report an Issue
                        </button>
                        <button wire:click="askGeneralQuestion" 
                                class="bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-sm hover:bg-blue-200 transition-colors">
                            💬 Ask a Question
                        </button>
                    </div>
                </div>
            @endif

            <!-- Message Input -->
            <div class="p-4 border-t border-gray-200">
                <form wire:submit="sendMessage" class="flex space-x-2">
                    <input type="text" wire:model="newMessage" 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Type your message..."
                           @if($isTyping) disabled @endif>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            @if($isTyping) disabled @endif>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('message-sent', () => {
            // Scroll to bottom when message is sent
            setTimeout(() => {
                const container = document.querySelector('[x-ref="messagesContainer"]');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }, 100);
        });
    });
</script>
