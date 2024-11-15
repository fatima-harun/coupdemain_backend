<?php

namespace App\Models;

;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Commentaire extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'employer_id', 'description'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Candidat
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id'); // Employeur
    }
}
