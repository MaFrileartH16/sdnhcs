<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
  use HasFactory;

  protected $fillable = [
    'guardian_user_id',
    'full_name',
    'gender',
    'guardian_relation',
  ];

  public function guardian(): BelongsTo
  {
    return $this->belongsTo(User::class, 'guardian_user_id');
  }

  public function admission(): Student|HasOne
  {
    return $this->hasOne(Admission::class);
  }
}
