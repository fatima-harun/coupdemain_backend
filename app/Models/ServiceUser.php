<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceUser extends Model
{
    use HasFactory;
    protected $table = 'user_service';
    protected $guarded = [];

    public function services(){
        return $this->belongsTo(Service::class);
    }

    public function employe(){
        return $this->belongsTo(User::class);
    }
}
