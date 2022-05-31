<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destinataire extends Model
{
    use HasFactory;

    public function filiere() { 
        return $this->belongsTo(Filiere::class); 
    }

    public function classe() { 
        return $this->belongsTo(Classe::class); 
    }

    public function user() { 
        return $this->belongsTo(User::class); 
    }

    public function notification() { 
        return $this->belongsTo(Notification::class); 
    }
}