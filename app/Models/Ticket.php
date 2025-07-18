<?php

namespace App\Models;

use App\Casts\Uuid;
use App\Enums\SenderTypeEnum;
use App\Enums\TicketStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Represents a support ticket in the system.
 *
 * A ticket is created by a user and can have multiple replies.
 * It also supports file attachments through Spatie MediaLibrary.
 */
class Ticket extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'uuid',
        'title',
        'message',
        'status',
        'sender_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * This ensures the 'status' attribute is automatically
     * cast to and from the TicketStatusEnum.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => TicketStatusEnum::class,
        'sender_type' => SenderTypeEnum::class,
        'uuid' => Uuid::class,
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }


    /**
     * Register the media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    /**
     * Get the route key for the model.
     *
     * This tells Laravel to use the 'uuid' column for route model binding
     * instead of the default 'id'.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the user that owns the ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the replies for the ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }
}
