# 🧠 Laravel AI Agent - Smart Customer Service Bot

A comprehensive Laravel 11 application that acts as an intelligent AI customer service agent. Built with local AI models (Ollama + LLaMA 2), this application provides natural conversations, automated quote generation, incident reporting, and admin management.

## ✨ Features

### 🤖 Intelligent AI Agent
- **Natural Conversations**: Powered by local LLaMA 2 model via Ollama
- **Context Awareness**: Maintains conversation history and context
- **Intent Recognition**: Automatically detects customer needs (quotes, incidents, general questions)
- **Professional Personality**: Acts as "Anwar from Brancom" with consistent, helpful responses

### 💬 Modern Chat Interface
- **Responsive Design**: Beautiful, modern UI built with Livewire and Tailwind CSS
- **Real-time Experience**: Typing indicators and smooth message flow
- **Quick Actions**: One-click buttons for common requests
- **Mobile Friendly**: Works seamlessly on all devices

### 📄 Quote Management
- **Automated Generation**: AI creates quotes based on conversation context
- **PDF Generation**: Professional PDF quotes using DomPDF
- **Email Integration**: Send quotes directly to customers
- **Tracking**: Status tracking from draft to accepted

### 🔧 Incident Reporting
- **Smart Detection**: AI identifies technical issues and creates incident reports
- **Priority System**: Automatic priority assignment based on keywords
- **Category Classification**: Organizes incidents by type (technical, billing, etc.)
- **Resolution Tracking**: Complete incident lifecycle management

### 📊 Admin Dashboard
- **Conversation Management**: View and manage all customer interactions
- **Analytics**: Track quotes, incidents, and conversation metrics
- **Manual Intervention**: Ability to step in when needed
- **Reporting**: Comprehensive reports and insights

## 🛠️ Technical Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Livewire 3, Tailwind CSS, Alpine.js
- **AI Integration**: Ollama (Local AI), LLaMA 2 model
- **Database**: MySQL/PostgreSQL/SQLite
- **PDF Generation**: DomPDF
- **Authentication**: Laravel Breeze
- **Real-time**: WebSockets ready (Laravel Echo/Pusher)

## 🚀 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL/PostgreSQL (or SQLite for development)
- Git

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd laravel-ai-agent
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 3: Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Environment

Edit `.env` file with your settings:

```env
# Application
APP_NAME="Laravel AI Agent"
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_agent
DB_USERNAME=your_username
DB_PASSWORD=your_password

# AI Configuration
OLLAMA_URL=http://127.0.0.1:11434
AI_MODEL=llama2
AI_AGENT_NAME=Anwar
COMPANY_NAME=Brancom

# Mail (for quote sending)
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@brancom.com
MAIL_FROM_NAME="Brancom AI Agent"
```

### Step 5: Database Setup

```bash
# Run migrations
php artisan migrate

# (Optional) Seed with sample data
php artisan db:seed
```

### Step 6: Install and Setup Ollama

#### Option A: Direct Installation

```bash
# Install Ollama
curl -fsSL https://ollama.ai/install.sh | sh

# Start Ollama service
ollama serve

# Pull LLaMA 2 model (in another terminal)
ollama pull llama2
```

#### Option B: Docker Installation

```bash
# Run Ollama in Docker
docker run -d -v ollama:/root/.ollama -p 11434:11434 --name ollama ollama/ollama

# Pull the model
docker exec -it ollama ollama pull llama2
```

### Step 7: Build Assets

```bash
# Build frontend assets
npm run build

# Or for development
npm run dev
```

### Step 8: Start the Application

```bash
# Start Laravel development server
php artisan serve
```

Visit `http://localhost:8000` to see your AI Agent in action!

## 📋 Usage

### For Customers

1. **Start a Chat**: Enter your name and email to begin
2. **Natural Conversation**: Talk to Anwar like a human customer service representative
3. **Request Quotes**: Ask for pricing and receive professional PDF quotes
4. **Report Issues**: Describe problems and get incident tickets automatically created
5. **Get Support**: Ask questions about services and get helpful responses

### For Administrators

1. **Access Admin Panel**: Visit `/admin` (add authentication as needed)
2. **Monitor Conversations**: View all customer interactions in real-time
3. **Manage Quotes**: Review, approve, and send quotes to customers
4. **Handle Incidents**: Track and resolve customer issues
5. **View Analytics**: Monitor performance and customer satisfaction

## 🔧 Configuration

### AI Model Configuration

The application supports multiple AI models through Ollama:

```env
# Available models (download with `ollama pull <model>`)
AI_MODEL=llama2          # Default, good balance of speed and quality
AI_MODEL=llama2:13b      # Larger model, better quality, slower
AI_MODEL=mistral         # Alternative model
AI_MODEL=codellama       # For technical support scenarios
```

### Customization

#### Agent Personality
Edit `app/Services/AiAgentService.php` to customize the agent's personality, responses, and behavior.

#### Intent Recognition
Modify the `detectIntent()` method to add new conversation types and triggers.

#### Response Templates
Update fallback responses in `getFallbackResponse()` for when AI is unavailable.

## 🎨 Customization

### Themes and Styling
- Edit `resources/css/app.css` for global styles
- Modify Tailwind configuration in `tailwind.config.js`
- Update Livewire components in `resources/views/livewire/`

### Adding New Features
1. Create new migrations for database changes
2. Add models and relationships
3. Create controllers and routes
4. Build Livewire components for UI
5. Update the AI service for new intents

## 🔒 Security Considerations

- **Local AI**: All AI processing happens locally, ensuring data privacy
- **Input Validation**: All user inputs are validated and sanitized
- **CSRF Protection**: Laravel's built-in CSRF protection is enabled
- **SQL Injection**: Eloquent ORM prevents SQL injection attacks
- **XSS Protection**: Blade templating escapes output by default

## 📈 Performance Optimization

### AI Response Speed
- Use smaller models for faster responses (llama2 vs llama2:13b)
- Implement response caching for common questions
- Consider GPU acceleration for Ollama

### Database Performance
- Add indexes for frequently queried fields
- Use database query optimization
- Implement caching for admin dashboards

### Frontend Performance
- Use Laravel Octane for improved performance
- Implement lazy loading for large conversation lists
- Add pagination for all data tables

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## 📝 API Documentation

The application provides RESTful APIs for integration:

### Chat API
- `POST /api/chat/start` - Start new conversation
- `POST /api/chat/message` - Send message
- `GET /api/chat/conversation/{id}` - Get conversation history

### Management APIs
- `GET /api/quotes` - List quotes
- `GET /api/incidents` - List incidents
- `POST /api/incidents/{id}/resolve` - Resolve incident

## 🚢 Deployment

### Production Setup

1. **Environment**: Set `APP_ENV=production` and `APP_DEBUG=false`
2. **Database**: Use production database (MySQL/PostgreSQL)
3. **AI Model**: Ensure Ollama is running with appropriate models
4. **Web Server**: Configure Nginx/Apache with PHP-FPM
5. **Process Manager**: Use Supervisor for queue workers
6. **SSL**: Enable HTTPS for production use

### Docker Deployment

```bash
# Build and run with Docker Compose
docker-compose up -d
```

### Cloud Deployment
- AWS: Use EC2 with load balancer
- DigitalOcean: Deploy on droplets
- Laravel Forge: Automated deployment and management

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Create an issue on GitHub
- Check the documentation
- Review the troubleshooting section below

## 🔧 Troubleshooting

### Common Issues

#### Ollama Connection Failed
```bash
# Check if Ollama is running
curl http://localhost:11434/api/version

# Restart Ollama service
systemctl restart ollama
```

#### AI Model Not Found
```bash
# List available models
ollama list

# Pull required model
ollama pull llama2
```

#### Database Connection Error
- Check database credentials in `.env`
- Ensure database server is running
- Verify database exists

#### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🛣️ Roadmap

- [ ] WebSocket integration for real-time chat
- [ ] Multi-language support
- [ ] Voice message support
- [ ] Advanced analytics dashboard
- [ ] CRM integration
- [ ] Mobile app
- [ ] AI model fine-tuning interface
- [ ] Custom training data management

---

Built with ❤️ using Laravel, Ollama, and modern web technologies.