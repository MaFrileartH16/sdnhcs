<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamScore extends Model
{
  use HasFactory;

  protected $fillable = [
    'admission_id',
    'exam_subject_id',
    'score',
  ];

  public function admission(): BelongsTo
  {
    return $this->belongsTo(Admission::class);
  }

  public function subject(): BelongsTo
  {
    return $this->belongsTo(ExamSubject::class, 'exam_subject_id');
  }
}
