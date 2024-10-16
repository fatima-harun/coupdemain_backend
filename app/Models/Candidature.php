<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidature extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'offre_id', 'date_candidature']; // Ajoutez ici

    public function notification(){
        return $this->belongsTo(Notification::class);
    }
}
