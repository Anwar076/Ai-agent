<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'incident_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'priority',
        'category',
        'subject',
        'description',
        'status',
        'attachments',
        'resolution',
        'resolved_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the conversation that owns the incident.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Generate a unique incident number.
     */
    public static function generateIncidentNumber(): string
    {
        $prefix = 'INC' . date('Y');
        $lastIncident = self::where('incident_number', 'like', $prefix . '%')
            ->orderBy('incident_number', 'desc')
            ->first();

        if ($lastIncident) {
            $lastNumber = (int) substr($lastIncident->incident_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Mark incident as resolved.
     */
    public function markAsResolved(string $resolution): void
    {
        $this->update([
            'status' => 'resolved',
            'resolution' => $resolution,
            'resolved_at' => Carbon::now(),
        ]);
    }

    /**
     * Scope to get open incidents.
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    /**
     * Scope to get high priority incidents.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }
}
