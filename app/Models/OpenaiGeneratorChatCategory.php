<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OpenaiGeneratorChatCategory extends Model
{
    use HasFactory;

    protected $table = 'openai_chat_category';

    public function cloned() : HasOne
    {
        return $this->hasOne(self::class, 'parent_id')->where('user_id', auth()->id());
    }

    public function scopeGlobal(Builder $query) : Builder
    {
        return $query->where('user_id', 0);
    }

    public function scopePersonal(Builder $query, $userId) : Builder
    {
        return $query->where('user_id', $userId);
    }
}
