<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admission extends Model
{
  use HasFactory;

  protected $fillable = [
    'student_id',
    'admission_form_file',
    'family_card_file',
    'birth_certificate_file',
    'guardian_id_card_file',
    'kindergarten_certificate_file',
    'average_score',
  ];

  public function student(): BelongsTo
  {
    return $this->belongsTo(Student::class);
  }

  public function examScores(): HasMany|Admission
  {
    return $this->hasMany(ExamScore::class);
  }
}
