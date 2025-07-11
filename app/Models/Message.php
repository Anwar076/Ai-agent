<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender',
        'content',
        'message_type',
        'metadata',
        'is_processed',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_processed' => 'boolean',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Scope to get user messages.
     */
    public function scopeFromUser($query)
    {
        return $query->where('sender', 'user');
    }

    /**
     * Scope to get AI agent messages.
     */
    public function scopeFromAi($query)
    {
        return $query->where('sender', 'ai_agent');
    }

    /**
     * Check if message is from AI agent.
     */
    public function isFromAi(): bool
    {
        return $this->sender === 'ai_agent';
    }
}
