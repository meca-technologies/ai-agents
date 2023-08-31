<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class UserOpenaiChat extends Model
{
    use HasFactory;

    protected $table = 'user_openai_chat';

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->uuid = self::generateUuid();
            $model->save();
        });
    }

    public function messages() {
        return $this->hasMany(UserOpenaiChatMessage::class);
    }

    public function category() {
        return $this->belongsTo(OpenaiGeneratorChatCategory::class, 'openai_chat_category_id', 'id' );
    }

    public static function generateUuid()
    {
        return Str::uuid()->toString();
    }
}
