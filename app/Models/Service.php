<?php

namespace App\Models;

use App\Models\Offre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;
   
    public function offres()
    {
        return $this->belongsToMany(Offre::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
