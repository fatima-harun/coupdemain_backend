<?php

namespace App\Models;

use App\Models\Candidature; // Import du modèle Candidature
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model 
{
    use HasFactory;

     protected $fillable = [
       'message',
     ];
   
    public function candidatures()
    {
        return $this->hasMany(Candidature::class); 
    }
}
