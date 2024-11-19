<?php

namespace App\Models;

;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Commentaire extends Model

{
    Use HasFactory;

    protected $fillable = ['candidat_id', 'employer_id', 'description', 'note'];

    // Relation avec le candidat
    public function candidat()
    {
        return $this->belongsTo(User::class, 'candidat_id');
    }

    // Relation avec l'employeur
    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }
}


