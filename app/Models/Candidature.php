<?php

namespace App\Models;

use App\Models\Notifications;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidature extends Model
{
    use HasFactory;

    protected $fillable = [
       'statut',
       'date_candidature'
    ];
    
    public function notification(){

        return $this->belongsTo(Notification::class);
    }
}
