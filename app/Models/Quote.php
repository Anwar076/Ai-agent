<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'quote_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'service_description',
        'amount',
        'currency',
        'status',
        'valid_until',
        'pdf_path',
        'line_items',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'valid_until' => 'date',
        'line_items' => 'array',
    ];

    /**
     * Get the conversation that owns the quote.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Generate a unique quote number.
     */
    public static function generateQuoteNumber(): string
    {
        $prefix = 'Q' . date('Y');
        $lastQuote = self::where('quote_number', 'like', $prefix . '%')
            ->orderBy('quote_number', 'desc')
            ->first();

        if ($lastQuote) {
            $lastNumber = (int) substr($lastQuote->quote_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if quote is expired.
     */
    public function isExpired(): bool
    {
        return $this->valid_until < Carbon::today();
    }

    /**
     * Scope to get active quotes.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['draft', 'sent']);
    }
}
