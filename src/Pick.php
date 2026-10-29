<?php

namespace HuseyinFiliz\Pickem;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property string $selected_outcome
 * @property bool|null $is_correct
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * * @property User $user
 * @property Event $event
 */
class Pick extends AbstractModel
{
    use HasFactory;
    use ScopeVisibilityTrait;

    // YENİ EKLENDİ: Zaman damgalarını otomatik yönet
    public $timestamps = true;

    protected $table = 'pickem_picks';

    protected $fillable = [
        'user_id',
        'event_id',
        'selected_outcome',
        'is_correct'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Query Scopes.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEvent($query, int $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    public function scopePending($query)
    {
        return $query->whereNull('is_correct');
    }

    /**
     * Helper Methods.
     */
    public function isCorrect(): bool
    {
        return true === $this->is_correct;
    }

    public function isIncorrect(): bool
    {
        return false === $this->is_correct;
    }

    public function isPending(): bool
    {
        return null === $this->is_correct;
    }

    public function hasResult(): bool
    {
        return null !== $this->is_correct;
    }

    /**
     * Get the outcome display name.
     */
    public function getOutcomeDisplayName(): string
    {
        return match ($this->selected_outcome) {
            Event::RESULT_HOME => $this->event?->homeTeam?->name ?? 'Home',
            Event::RESULT_AWAY => $this->event?->awayTeam?->name ?? 'Away',
            Event::RESULT_DRAW => 'Draw',
            default => 'Unknown',
        };
    }

    /**
     * Check if pick can be changed.
     */
    public function canBeChanged(): bool
    {
        return $this->event && $this->event->canPick();
    }

    /**
     * Check if pick can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->event && $this->event->canPick();
    }
}
