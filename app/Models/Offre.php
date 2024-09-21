<?php

namespace App\Models;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offre extends Model
{
    use HasFactory;

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
}
