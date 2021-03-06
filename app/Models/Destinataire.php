<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destinataire extends Model
{
    use HasFactory;
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    
    public function user() { 
        return $this->belongsTo(User::class); 
    }

    public function notification() { 
        return $this->belongsTo(Notification::class); 
    }
}
