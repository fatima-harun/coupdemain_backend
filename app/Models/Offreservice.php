<?php

namespace App\Models;

use App\Models\Offre;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offreservice extends Model
{
    use HasFactory;
    
    //relation avec offre
    public function offre(){
        return $this->belongsTo(Offre::class);
    }
    public function services(){
        return $this->belongsTo(Service::class);
    }
}

