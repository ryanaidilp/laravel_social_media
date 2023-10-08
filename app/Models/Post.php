<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Xetaio\Mentions\Models\Traits\HasMentionsTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory, HasMentionsTrait;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the user that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function scopeByFollowing($query)
    {
        return $query->whereRelation('user.followables', 'user_id', auth()->user()->id)
            ->orWhere('user_id', auth()->user()->id);
    }

    public function scopeMyPosts($query)
    {
        return $query->where('user_id', auth()->user()->id);
    }
}
