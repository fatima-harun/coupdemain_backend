<?php

namespace App\Models;

use App\Models\Candidature; // Import du modèle Candidature
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [ 'user_id', 'message', 'read', 'candidature_id', ];

    public function user() {

        return $this->belongsTo(User::class);

    }

    public function candidature() {

        return $this->belongsTo(Candidature::class);

    }
}
