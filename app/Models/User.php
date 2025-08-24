<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use HasFactory;

  protected $fillable = [
    'name',
    'role',
    'phone',
    'password',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected $casts = [
    'password' => 'hashed',
  ];

  public function students(): User|HasMany
  {
    return $this->hasMany(Student::class, 'guardian_user_id');
  }
}
