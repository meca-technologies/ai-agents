<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
  use HasFactory;

  protected $fillable = [
    'start_time',
    'finish_time',
    'comments',
    'client_id',
    'employee_id',
  ];

  public function client(): BelongsTo
  {
    return $this->belongsTo(Clients::class);
  }

  public function employee(): BelongsTo
  {
    return $this->belongsTo(Employee::class);
  }
}
