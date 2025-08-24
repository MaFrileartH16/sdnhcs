<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSubject extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'link_url',
    'max_score',
  ];

  // Relations
  public function examScores(): HasMany|ExamSubject
  {
    return $this->hasMany(ExamScore::class);
  }
}
