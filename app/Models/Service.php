<?php

namespace App\Models;

use App\Models\Offre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function offres()
    {
        return $this->belongsToMany(Offre::class,'offre_service');
    }

    public function employe()
    {
        return $this->belongsToMany(User::class,'user_service');
    }
}
