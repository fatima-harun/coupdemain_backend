<?php

namespace App\Models;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offre extends Model
{
    use HasFactory;

    protected $guarded = [

    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'offre_service');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employe()
    {
        return $this->belongsToMany(User::class);
    }
}
