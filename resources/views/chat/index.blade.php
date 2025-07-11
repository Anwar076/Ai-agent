<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - AI Chat Agent</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-xl font-bold text-gray-900">🤖 Brancom AI Agent</h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">Powered by Local AI</span>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6 text-center">
                    <h2 class="text-3xl font-bold text-gray-900">Welcome to Brancom</h2>
                    <p class="mt-2 text-gray-600">Chat with Anwar, our intelligent AI customer service agent</p>
                </div>

                <!-- Chat Interface -->
                <div class="max-w-4xl mx-auto">
                    <livewire:chat-interface />
                </div>

                <!-- Features -->
                <div class="mt-12 max-w-6xl mx-auto">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900">What can Anwar help you with?</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Get Quotes</h4>
                                <p class="text-gray-600">Request personalized quotes for our services with automated PDF generation</p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Report Issues</h4>
                                <p class="text-gray-600">Submit and track technical issues with automated incident management</p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Ask Questions</h4>
                                <p class="text-gray-600">Get instant answers about our services, pricing, and general inquiries</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Info -->
                <div class="mt-12 bg-gray-50 rounded-lg p-6">
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">🚀 Powered by Local AI</h3>
                        <p class="text-gray-600">This chat agent runs on a free, local AI model using Ollama with LLaMA 2. No data is sent to external services, ensuring your privacy and reducing costs.</p>
                        <div class="mt-4 flex justify-center space-x-6 text-sm text-gray-500">
                            <span>✅ Privacy-First</span>
                            <span>✅ Cost-Effective</span>
                            <span>✅ Fast Response</span>
                            <span>✅ 24/7 Available</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 text-sm">
                    <p>&copy; {{ date('Y') }} Brancom. AI Agent powered by Laravel & Ollama.</p>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>