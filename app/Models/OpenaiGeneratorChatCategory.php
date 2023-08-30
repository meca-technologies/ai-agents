<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenaiGeneratorChatCategory extends Model
{
  use HasFactory;

  protected $table = 'openai_chat_category';


  public function cloned()
  {
    return $this->hasOne(self::class, 'parent_id')->where('user_id', auth()->id());
  }

  public function scopeGlobal(Builder $query)
  {
    return $query->where('user_id', 0);
  }


  public function scopePersonal(Builder $query, $userId)
  {
    return $query->where('user_id', $userId);
  }
}
